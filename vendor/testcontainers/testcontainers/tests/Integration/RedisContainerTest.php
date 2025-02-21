<?php

declare(strict_types=1);

namespace Testcontainers\Tests\Integration;

use Predis\Client;
use Testcontainers\Modules\RedisContainer;

class RedisContainerTest extends ContainerTestCase
{
    public function setUp(): void
    {
        $this->container = (new RedisContainer())
            ->start();
    }

    public function testRedisContainer(): void
    {
        $redisClient = new Client([
            'host' => $this->container->getHost(),
            'port' => $this->container->getFirstMappedPort(),
        ]);

        $redisClient->ping();

        $this->assertTrue($redisClient->isConnected());

        $redisClient->set('greetings', 'Hello, World!');

        $this->assertEquals('Hello, World!', $redisClient->get('greetings'));
    }
}
