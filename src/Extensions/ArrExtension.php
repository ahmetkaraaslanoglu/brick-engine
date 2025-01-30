<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class ArrExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->namespaces['arr'] = [
            'length' => fn ($array) => count($array),
            'keys' => fn ($array) => array_keys($array),
            'values' => fn ($array) => array_values($array),
            'merge' => fn (...$arrays) => array_merge(...$arrays),
        ];
    }
}
