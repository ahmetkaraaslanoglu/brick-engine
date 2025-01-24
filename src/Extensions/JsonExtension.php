<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Value;

class JsonExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context->functions['json_encode'] = fn(Value $arguments) => $this->json_encode($arguments);
        $this->engine->context->functions['json_decode'] = fn(Value $arguments) => $this->json_decode($arguments);
    }

    public function json_encode(Value $arguments): Value
    {
        $data = $this->engine->context->value($arguments)->data;
        return Value::from(json_encode($data));
    }

    public function json_decode(Value $arguments): Value
    {
        $data = $this->engine->context->value($arguments)->data;
        return Value::from(json_decode($data, true));
    }
}
