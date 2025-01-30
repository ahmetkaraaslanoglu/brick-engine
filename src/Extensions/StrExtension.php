<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class StrExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->namespaces['str'] = [
            'length' => fn ($string) => strlen($string),
            'contains' => fn ($string, $needle) => str_contains($string, $needle),
            'replace' => fn ($string, $search, $replace) => str_replace($search, $replace, $string),
            'split' => fn ($string, $delimiter) => str_split($string, $delimiter),
            'repeat' => fn ($string, $times) => str_repeat($string, $times),
            'shuffle' => fn ($string) => str_shuffle($string),
            'starts_with' => fn ($string, $needle) => str_starts_with($string, $needle),
            'ends_with' => fn ($string, $needle) => str_ends_with($string, $needle),
            'trim' => fn ($string) => trim($string),
            'ucfirst' => fn ($string) => ucfirst($string),
            'ucwords' => fn ($string) => ucwords($string),
            'lcfirst' => fn ($string) => lcfirst($string),
            'substr' => fn ($string, $start, $length = null) => substr($string, $start, $length),
            'explode' => fn ($string, $delimiter) => explode($delimiter, $string),
            'implode' => fn ($glue, $pieces) => implode($glue, $pieces),
        ];
    }
}
