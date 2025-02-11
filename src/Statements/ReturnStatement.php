<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
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

    public function run(Runtime $runtime, Context $context): ExecutionResult
    {
        parent::run($runtime, $context);

        $value = $this->value?->run($runtime, $context);
        return new ExecutionResult(
            value: $value,
            return: true,
        );
    }

    public function compile(): string
    {
        if ($this->value) {
            $value = $this->value->compile();
            return "return {$value};";
        }

        return "return;";
    }
}
