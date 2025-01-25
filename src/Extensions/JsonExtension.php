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
        $this->engine->context->functions['json_encode'] = fn(Context $context) => $this->json_encode($context);
        $this->engine->context->functions['json_decode'] = fn(Context $context) => $this->json_decode($context);
    }

    public function json_encode(Context $context): Value
    {
        $argument = $context->arguments[0];
        $data = $context->value($argument)->data;

        return Value::from(json_encode($data));
    }

    public function json_decode(Context $context): Value
    {
        $argument = $context->arguments[0];
        $data = $context->value($argument)->data;

        return Value::from(json_decode($data, true));
    }
}
