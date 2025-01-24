<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Value;

class ConsoleExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->functions['print'] = fn(Value $argument) => $this->print($argument);
        $this->engine->context->functions['println'] = fn(Value $argument) => $this->println($argument);
    }

    public function print(Value $argument): Value
    {
        $value = $this->engine->context->value($argument);
        print($value);
        return new Value(ValueType::Void);
    }

    public function println(Value $argument): Value
    {
        $value = $this->engine->context->value($argument);
        print($value . PHP_EOL);
        return new Value(ValueType::Void);
    }
}
