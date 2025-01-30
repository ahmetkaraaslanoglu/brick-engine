<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class BreakStatement extends Node implements StatementInterface
{
    public function __construct()
    {
        parent::__construct([
            'type' => 'BREAK',
        ]);
    }

    public function run(Runtime $runtime, Context $context): ExecutionResult
    {
        parent::run($runtime, $context);

        return new ExecutionResult(
            break: true,
        );
    }

    public function compile(): string
    {
        return "break;";
    }
}
