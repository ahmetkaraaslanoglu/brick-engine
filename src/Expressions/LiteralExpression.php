<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\InvalidLiteralException;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class LiteralExpression extends Node implements ExpressionInterface
{
    public function __construct(string $type, mixed $value)
    {
        parent::__construct([
            'type' => 'LITERAL',
            'literal' => $type,
            'raw' => $value,
            'value' => '',
        ]);
    }

    public function run(Context $context): Value
    {
        if ($this->literal === 'NUMBER') {
            return new Value(
                type: ValueType::Numeric,
                data: is_float($this->raw) ? floatval($this->raw) : intval($this->raw),
            );
        }

        if ($this->literal === 'STRING') {
            return new Value(
                type: ValueType::String,
                data: strval($this->raw),
            );
        }

        if ($this->literal === 'TRUE' || $this->literal === 'FALSE') {
            return new Value(
                type: ValueType::Boolean,
                data: $this->literal === 'TRUE',
            );
        }

        if ($this->literal === 'NULL') {
            return new Value(
                type: ValueType::Null,
                data: null,
            );
        }

        throw new InvalidLiteralException($this->literal);
    }
}
