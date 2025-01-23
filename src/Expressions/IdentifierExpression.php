<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class IdentifierExpression extends Node implements ExpressionInterface
{
    public function __construct(string $identifier)
    {
        parent::__construct([
            'type' => 'IDENTIFIER',
            'value' => $identifier,
        ]);
    }

    public function run(Context $context): Value
    {
        return new Value(ValueType::Identifier, $this->value);
    }
}
