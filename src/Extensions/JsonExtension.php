<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class JsonExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->functions['json_encode'] = fn (...$args) => json_encode($args[0]);
        $this->engine->context->functions['json_decode'] = fn (...$args) => json_decode($args[0], true);
    }
}
