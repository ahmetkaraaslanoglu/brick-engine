<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\InvalidLiteralException;
use IsaEken\BrickEngine\Runtime;
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

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        if ($this->literal === 'NUMBER') {
            return new Value(
                context: $context,
                type: ValueType::Numeric,
                data: is_float($this->raw) ? floatval($this->raw) : intval($this->raw),
            );
        }

        if ($this->literal === 'STRING') {
            return new Value(
                context: $context,
                type: ValueType::String,
                data: strval($this->raw),
            );
        }

        if ($this->literal === 'TRUE' || $this->literal === 'FALSE') {
            return new Value(
                context: $context,
                type: ValueType::Boolean,
                data: $this->literal === 'TRUE',
            );
        }

        if ($this->literal === 'NULL') {
            return new Value(
                context: $context,
                type: ValueType::Null,
                data: null,
            );
        }

        throw new InvalidLiteralException($this->literal);
    }

    public function compile(): string
    {
        if ($this->literal === 'STRING') {
            return "\"$this->raw\"";
        }

        return $this->raw; // @todo update this to value
    }
}
