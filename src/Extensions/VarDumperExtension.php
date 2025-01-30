<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class VarDumperExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
    }

    public function register(): void
    {
        $this->engine->context->functions['dump'] = [$this, 'dump'];
    }

    public function dump(...$arguments)
    {
        foreach ($arguments as $argument) {
            print_r($argument);
            print("\n");
        }
    }
}
