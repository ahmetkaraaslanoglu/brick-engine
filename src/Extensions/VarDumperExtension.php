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
        $this->engine->context->functions['dump'] = fn(Context $context) => $this->dump($context);
    }

    public function dump(Context $context): Value
    {
        foreach ($context->arguments as $argument) {
            if ($argument->is(ValueType::Identifier)) {
                $value = $context->variables[$argument->data];
                print($value . PHP_EOL);
            } else {
                print($argument . PHP_EOL);
            }
        }

        return new Value(ValueType::Void);
    }
}
