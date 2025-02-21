<?php

declare(strict_types=1);

namespace Testcontainers\Container;

enum HttpMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';

    public static function fromString(string $method): self
    {
        return self::tryFrom(strtoupper($method)) ?? throw new \InvalidArgumentException("Invalid HTTP method: $method");
    }
}
