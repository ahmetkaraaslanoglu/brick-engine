<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Value;

class VarDumperExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
    }

    public function register(): void
    {
        $this->engine->context->functions['dump'] = fn(Value $argument) => $this->dump($argument);
    }

    public function dump(Value $argument): Value
    {
        if ($argument->is(ValueType::Identifier)) {
            $value = $this->engine->context->variables[$argument->data];
            print($value . PHP_EOL);
            return new Value(ValueType::Void);
        }

        print($argument . PHP_EOL);
        return new Value(ValueType::Void);
    }
}
