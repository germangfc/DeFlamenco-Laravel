<?php

declare(strict_types=1);

namespace Testcontainers\Tests\Integration;

use Testcontainers\Modules\PostgresContainer;

class PostgreSQLContainerTest extends ContainerTestCase
{
    public function setUp(): void
    {
        $this->container = (new PostgresContainer())
            ->withPostgresUser('bar')
            ->withPostgresDatabase('foo')
            ->start();
    }

    public function testPostgreSQLContainer(): void
    {
        $pdo = new \PDO(
            sprintf(
                'pgsql:host=%s;port=%d;dbname=foo',
                $this->container->getHost(),
                $this->container->getFirstMappedPort()
            ),
            'bar',
            'test',
        );

        $query = $pdo->query('SELECT datname FROM pg_database');

        $this->assertInstanceOf(\PDOStatement::class, $query);

        $databases = $query->fetchAll(\PDO::FETCH_COLUMN);

        $this->assertContains('foo', $databases);
    }
}
