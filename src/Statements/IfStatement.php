<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class IfStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface $condition, StatementInterface $then, StatementInterface|null $else)
    {
        parent::__construct([
            'type' => 'IF',
            'condition' => $condition,
            'then' => $then,
            'else' => $else,
        ]);
    }

    public function run(Runtime $runtime, Context $context): ExecutionResult
    {
        parent::run($runtime, $context);

        $condition = $this->condition->run($context)->isTruthy();

        if ($condition) {
            return $this->then->run($context);
        } else if ($this->else) {
            return $this->else->run($context);
        }

        return new ExecutionResult();
    }

    public function compile(): string
    {
        $condition = $this->condition->compile();
        $then = $this->then->compile();

        if ($this->else) {
            $else = $this->else->compile();
            return "if ($condition) $then else $else";
        }

        return "if ($condition) $then";
    }
}
