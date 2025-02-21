<?php

declare(strict_types=1);

namespace Testcontainers\Utils;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class TarBuilder
{
    /**
     * @var array<array{source: string, target: string, mode: int|null}>
     */
    private array $files = [];

    /**
     * @var array<array{source: string, target: string, mode: int|null}>
     */
    private array $directories = [];

    /**
     * @var array<array{content: string, target: string, mode: int|null}>
     */
    private array $contents = [];

    /**
     * Add a single file from the local filesystem.
     */
    public function addFile(string $source, string $target, ?int $mode = null): self
    {
        if (!is_file($source)) {
            throw new InvalidArgumentException("Invalid file path: {$source}");
        }
        if (empty($target)) {
            throw new InvalidArgumentException("Target path cannot be empty.");
        }
        if ($mode !== null && ($mode < 0 || $mode > 0o777)) {
            throw new InvalidArgumentException("Invalid mode for file: {$mode}");
        }
        $this->files[] = [
            'source' => $source,
            'target' => $target,
            'mode'   => $mode,
        ];
        return $this;
    }

    /**
     * Add a directory (recursively) from the local filesystem.
     */
    public function addDirectory(string $source, string $target, ?int $mode = null): self
    {
        $this->directories[] = [
            'source' => $source,
            'target' => $target,
            'mode'   => $mode,
        ];
        return $this;
    }

    /**
     * Add inline string content that should become a file in the tar.
     */
    public function addContent(string $content, string $target, ?int $mode = null): self
    {
        $this->contents[] = [
            'content' => $content,
            'target'  => $target,
            'mode'    => $mode,
        ];
        return $this;
    }

    /**
     * Builds the .tar archive from everything that was added (files, directories, contents).
     *
     * Returns the full path to the created .tar file.
     */
    public function buildTarArchive(): string
    {
        $tempDir = $this->createTempDir();

        $this->copyFilesToLocalDir($tempDir, $this->files);
        $this->copyDirectoriesToLocalDir($tempDir, $this->directories);
        $this->createFilesFromContent($tempDir, $this->contents);

        $tarFilePath = $this->createTempTarPath();
        $this->runTarCommand($tarFilePath, $tempDir);
        $this->removeDirectoryRecursively($tempDir);

        return $tarFilePath;
    }

    public function clear(): void
    {
        $this->files = [];
        $this->directories = [];
        $this->contents = [];
    }

    private function createTempDir(): string
    {
        $tmpDirName = tempnam(sys_get_temp_dir(), 'tc_files_');
        if ($tmpDirName === false) {
            throw new RuntimeException("Failed to create a temp file for tar data");
        }
        // tempnam() creates a file; remove it and create directory instead
        unlink($tmpDirName);

        if (!mkdir($tmpDirName) && !is_dir($tmpDirName)) {
            throw new RuntimeException("Failed to create temp directory: {$tmpDirName}");
        }

        return $tmpDirName;
    }

    private function createTempTarPath(): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'tc_tar_');

        if ($tmpFile === false) {
            throw new RuntimeException("Failed to create temp file for tar archive");
        }

        $tarFilePath = $tmpFile . '.tar';

        if (!rename($tmpFile, $tarFilePath)) {
            throw new RuntimeException("Failed renaming temp file to .tar");
        }
        return $tarFilePath;
    }

    private function runTarCommand(string $tarFilePath, string $sourceDir): void
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            $additionalFlags = ' --disable-copyfile --no-xattrs';
        } else {
            $additionalFlags = '';
        }

        // without --disable-copyfile and --no-xattrs combination, tar will fail on macOS
        $cmd = sprintf(
            'tar %s -cf %s -C %s . 2>&1',
            $additionalFlags,
            escapeshellarg($tarFilePath),
            escapeshellarg($sourceDir)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $errorText = implode("\n", $output);
            throw new RuntimeException("Failed to create tar archive:\n{$errorText}");
        }
    }

    private function removeDirectoryRecursively(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if (!$item instanceof SplFileInfo) {
                continue;
            }
            $path = $item->getRealPath();
            if ($item->isDir()) {
                rmdir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * @param array<array{source: string, target: string, mode: int|null}> $files
     */
    private function copyFilesToLocalDir(string $tempDir, array $files): void
    {
        foreach ($files as $file) {
            $source = $file['source'];
            $target = $file['target'];
            $mode   = $file['mode'] ?? null;

            if (!is_file($source)) {
                throw new InvalidArgumentException("File not found: $source");
            }
            $destPath = $this->makeDestPath($tempDir, $target);
            $this->ensureParentDir($destPath);

            if (!copy($source, $destPath)) {
                throw new RuntimeException("Failed to copy file $source to $destPath");
            }
            if ($mode !== null) {
                chmod($destPath, $mode);
            }
        }
    }

    /**
     * @param array<array{source: string, target: string, mode: int|null}> $directories
     */
    private function copyDirectoriesToLocalDir(string $tempDir, array $directories): void
    {
        foreach ($directories as $dir) {
            $source = $dir['source'];
            $target = $dir['target'];
            $mode   = $dir['mode'] ?? null;

            if (!is_dir($source)) {
                throw new InvalidArgumentException("Directory not found: $source");
            }
            $destPath = $this->makeDestPath($tempDir, $target);
            $this->copyDirectoryRecursively($source, $destPath);

            if ($mode !== null) {
                chmod($destPath, $mode);
            }
        }
    }

    /**
     * @param array<array{content: string, target: string, mode: int|null}> $contents
     */
    private function createFilesFromContent(string $tempDir, array $contents): void
    {
        foreach ($contents as $content) {
            $data   = $content['content'];
            $target = $content['target'];
            $mode   = $content['mode'] ?? null;

            $destPath = $this->makeDestPath($tempDir, $target);
            $this->ensureParentDir($destPath);

            file_put_contents($destPath, $data);
            if ($mode !== null) {
                chmod($destPath, $mode);
            }
        }
    }

    private function copyDirectoryRecursively(string $sourceDir, string $destDir): void
    {
        $this->ensureParentDir($destDir);

        $innerIterator = new RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS);

        /** @var RecursiveIteratorIterator<RecursiveDirectoryIterator> $iterator */
        $iterator = new RecursiveIteratorIterator(
            $innerIterator,
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if (!$item instanceof SplFileInfo) {
                continue;
            }

            /** @var RecursiveDirectoryIterator $innerIterator */
            $innerIterator = $iterator->getInnerIterator();
            $subPathName = $innerIterator->getSubPathName();
            $targetPath = $destDir . '/' . $subPathName;

            // Ensure the parent directory for the target path exists
            $this->ensureParentDir($targetPath);

            if ($item->isDir()) {
                if (!mkdir($targetPath, 0o777, true) && !is_dir($targetPath)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $targetPath));
                }
            } else {
                copy($item->getPathname(), $targetPath);
            }
        }
    }

    private function makeDestPath(string $tempDir, string $target): string
    {
        return rtrim($tempDir, '/') . '/' . ltrim($target, '/');
    }

    private function ensureParentDir(string $path): void
    {
        $parent = dirname($path);
        if (!is_dir($parent) && !mkdir($parent, 0o777, true) && !is_dir($parent)) {
            throw new RuntimeException("Failed to create parent directory: $parent");
        }
    }
}
