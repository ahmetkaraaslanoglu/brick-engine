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
        $this->engine->context->functions['print'] = fn(Context $context) => $this->print($context);
        $this->engine->context->functions['println'] = fn(Context $context) => $this->println($context);
    }

    public function print(Context $context): Value
    {
        foreach ($context->arguments as $argument) {
            print(sprintf("%s", $context->value($argument)->data));
        }

        return new Value(ValueType::Void);
    }

    public function println(Context $context): Value
    {
        foreach ($context->arguments as $argument) {
            print(sprintf("%s\n", $context->value($argument)->data));
        }

        return new Value(ValueType::Void);
    }
}
