<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class TimeExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->namespaces['time'] = [
            'sleep' => function ($ms) {
                usleep($ms * 1000);
            },
            'now' => fn () => time(),
            'date' => fn ($format, $timestamp = null) => date($format, $timestamp ?? time()),
            'microtime' => fn ($get_as_float = false) => microtime($get_as_float),
        ];
    }
}
