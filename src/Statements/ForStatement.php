<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class ForStatement extends Node implements StatementInterface
{
    public function __construct(StatementInterface|null $init, ExpressionInterface|null $condition, ExpressionInterface|null $update, StatementInterface $body)
    {
        parent::__construct([
            'type' => 'FOR',
            'init' => $init,
            'condition' => $condition,
            'update' => $update,
            'body' => $body,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $this->init->run($context);
        $result = null;

        while ($this->condition->run($context)->isTruthy()) {
            $result = $this->body->run($context);
            $this->update->run($context);
        }

        return $result;
    }

    public function compile(): string
    {
        $init = $this->init->compile();
        $condition = $this->condition->compile();
        $update = $this->update->compile();
        $body = $this->body->compile();

        return "for ($init; $condition; $update) $body";
    }
}
