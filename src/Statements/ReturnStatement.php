<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class ReturnStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface|null $value)
    {
        parent::__construct([
            'type' => 'RETURN',
            'value' => $value,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $value = $this->value->run($context);
        return new ExecutionResult(
            value: $value,
            return: true,
        );
    }
}
