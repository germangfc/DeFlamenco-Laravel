<?php

declare(strict_types=1);

namespace Testcontainers\Wait;

use Testcontainers\Container\StartedTestContainer;
use Testcontainers\Exception\ContainerWaitingTimeoutException;

class WaitForHostPort extends BaseWaitStrategy
{
    public function wait(StartedTestContainer $container): void
    {
        $startTime = microtime(true) * 1000;

        while (true) {
            $elapsedTime = (microtime(true) * 1000) - $startTime;

            if ($elapsedTime > $this->timeout) {
                throw new ContainerWaitingTimeoutException($container->getId());
            }

            if ($this->boundPortsOpened($container)) {
                return; // Port is open, container is ready
            }

            usleep($this->pollInterval * 1000); // Wait for the next polling interval
        }
    }

    /**
     * @param StartedTestContainer $container
     * @return bool
     */
    private function boundPortsOpened(StartedTestContainer $container): bool
    {
        $boundPorts = $container->getBoundPorts();
        foreach ($boundPorts as $bindings) {
            foreach ($bindings as $binding) {
                $hostIp = trim($binding->getHostIp() ?? '');
                if ($hostIp === '' || $hostIp === '0.0.0.0') {
                    $hostIp = $container->getHost();
                }
                $hostPort = (int)$binding->getHostPort();
                if (!$this->isPortOpen($hostIp, $hostPort)) {
                    return false;
                }
            }
        }
        return true;
    }

    private function isPortOpen(string $ipAddress, int $port): bool
    {
        $connection = @fsockopen($ipAddress, $port, $errno, $errstr, 2);

        if ($connection !== false) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
