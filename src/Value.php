<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Enums\ValueType;

class Value
{
    public ValueType $type;

    public mixed $data;

    public function __construct(ValueType $type, mixed $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function is(ValueType $type): bool
    {
        return $this->type === $type;
    }

    public function isTruthy(): bool
    {
        return match ($this->type) {
            ValueType::Boolean => $this->data === true,
            ValueType::Numeric => $this->data !== 0,
            ValueType::String => $this->data !== '',
            ValueType::Array => count($this->data) > 0,
            ValueType::Null => false,
            ValueType::Identifier => true,
            ValueType::ArrayElement => true,

            default => false,
        };
    }
}
