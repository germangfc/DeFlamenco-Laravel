<?php

namespace App\utils;

class GuuidGenerator
{
    public static function generateHash(): string
    {
        $bytes = random_bytes(8);

        $base64 = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');

        return $base64;
    }
}
