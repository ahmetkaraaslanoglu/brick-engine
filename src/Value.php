<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\InternalCriticalException;

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

    public function __toString(): string
    {
        if ($this->type === ValueType::Null) {
            return 'null';
        }

        if ($this->type === ValueType::Numeric) {
            return sprintf('num(%s)', $this->data);
        }

        if ($this->type === ValueType::String) {
            return sprintf('str(%s) "%s"', strlen($this->data), $this->data);
        }

        if ($this->type === ValueType::Boolean) {
            return sprintf('bool(%s)', $this->data ? 'true' : 'false');
        }

        if ($this->type === ValueType::Array) {
            $items = [];
            foreach ($this->data as $key => $item) {
                $items[] = sprintf('  [%s] => %s', $key, $item);
            }

            return sprintf("array(%s) [\n%s\n]", count($this->data), implode("\n", $items));
        }

        if ($this->type === ValueType::Function) {
            return 'function';
        }

        if ($this->type === ValueType::Void) {
            return 'void';
        }

        if ($this->type === ValueType::Identifier) {
            $value = $this->data;
            if ($this->data instanceof Value) {
                $value = $this->data->value;
            }

            return sprintf('id(%s) "%s"', $this->data, $value);
        }

        return sprintf('UNKNOWN(%s)', $this->type->value);
    }

    public static function real(Value $value): mixed
    {
        return match ($value->type) {
            ValueType::Boolean => boolval($value->data),
            ValueType::Numeric => is_int($value->data) ? intval($value->data) : floatval($value->data),
            ValueType::String => strval($value->data),
            ValueType::Array => (function () use ($value) {
                $array = [];
                foreach ($value->data as $item) {
                    $array[] = self::real($item);
                }

                return $array;
            })(),
            ValueType::Identifier => $value->data, // @todo: get value from variable
            ValueType::Function => $value->data, // @todo: function reference
            default => null,
        };
    }

    public static function from(mixed $value): Value
    {
        if (is_bool($value)) {
            return new Value(ValueType::Boolean, $value);
        }

        if (is_numeric($value)) {
            return new Value(ValueType::Numeric, $value);
        }

        if (is_string($value)) {
            return new Value(ValueType::String, $value);
        }

        if (is_array($value)) {
            $items = array_map(function ($item) {
                return Value::from($item);
            }, $value);

            return new Value(ValueType::Array, $items);
        }

        if ($value === null) {
            return new Value(ValueType::Null);
        }

        throw new InternalCriticalException("Unsupported value type: " . gettype($value));
    }
}
