<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ParamDefinitionExpression extends Node implements ExpressionInterface
{
    public function __construct(string $identifier, StatementInterface|null $defaultValue)
    {
        parent::__construct([
            'type' => 'FUNCTION_ARGUMENT',
            'identifier' => $identifier,
            'default_value' => $defaultValue,
        ]);
    }

    public function run(Context $context): Value
    {

    }
}
