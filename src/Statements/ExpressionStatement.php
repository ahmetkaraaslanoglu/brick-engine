<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class ExpressionStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface $expression)
    {
        parent::__construct([
            'type' => 'EXPRESSION',
            'expression' => $expression,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        return new ExecutionResult($this->expression->run($context));
    }

    public function compile(): string
    {
        // TODO: Implement compile() method.
    }
}
