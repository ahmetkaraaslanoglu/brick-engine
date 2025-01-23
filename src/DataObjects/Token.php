<?php

namespace IsaEken\BrickEngine\DataObjects;

class Token
{
    public function __construct(
        public string $token,
        public string $value,
        public int $start,
        public int $end,
    )
    {
        // ...
    }

    public static function make(string $token, string $value, int $start, int|null $end = null): Token
    {
        if ($end === null) {
            $end = $start + strlen($value);
        }

        return new Token($token, $value, $start, $end);
    }
}
