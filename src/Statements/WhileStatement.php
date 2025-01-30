<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
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

    public function run(Runtime $runtime, Context $context): ExecutionResult
    {
        parent::run($runtime, $context);

        $result = null;

        while ($this->condition->run($context)->isTruthy()) {
            $result = $this->body->run($context);

            if ($result->break) {
                break;
            }
        }

        return $result ?? new ExecutionResult();
    }

    public function compile(): string
    {
        $condition = $this->condition->compile();
        $body = $this->body->compile();
        return "while ($condition) $body";
    }
}
