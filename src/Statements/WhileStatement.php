<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class WhileStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface $condition, StatementInterface $body)
    {
        parent::__construct([
            'type' => 'WHILE',
            'condition' => $condition,
            'body' => $body,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $result = null;

        while ($this->condition->run($context)->isTruthy()) {
            $result = $this->body->run($context);
        }

        return $result;
    }
}
