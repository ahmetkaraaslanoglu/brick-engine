<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class ConsoleExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->functions['print'] = [$this, 'print'];
        $this->engine->context->functions['println'] = [$this, 'println'];
    }

    public function print(...$arguments)
    {
        foreach ($arguments as $argument) {
            print(sprintf("%s", $argument));
        }
    }

    public function println(...$arguments)
    {
        foreach ($arguments as $argument) {
            print(sprintf("%s\n", $argument));
        }
    }
}
