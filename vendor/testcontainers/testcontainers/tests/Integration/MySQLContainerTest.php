<?php

declare(strict_types=1);

namespace Testcontainers\Tests\Integration;

use Testcontainers\Modules\MySQLContainer;

class MySQLContainerTest extends ContainerTestCase
{
    public function setUp(): void
    {
        $this->container = (new MySQLContainer())
            ->withMySQLDatabase('foo')
            ->withMySQLUser('bar', 'baz')
            ->start();
    }

    public function testMySQLContainer(): void
    {
        $pdo = new \PDO(
            sprintf(
                'mysql:host=%s;port=%d',
                $this->container->getHost(),
                $this->container->getFirstMappedPort()
            ),
            'bar',
            'baz',
        );

        $query = $pdo->query('SHOW databases');

        $this->assertInstanceOf(\PDOStatement::class, $query);

        $databases = $query->fetchAll(\PDO::FETCH_COLUMN);

        $this->assertContains('foo', $databases);
    }
}
