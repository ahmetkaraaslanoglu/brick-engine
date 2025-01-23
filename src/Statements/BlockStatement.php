<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class BlockStatement extends Node implements StatementInterface
{
    public function __construct(array $statements)
    {
        parent::__construct([
            'type' => 'BLOCK',
            'statements' => $statements,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        /** @var StatementInterface $statement */
        foreach ($this->data['statements'] as $statement) {
            $result = $statement->run(
                $context,
            );

            if ($result->return) {
                if ($result->value) {
                    $result->value = $context->value($result->value);
                }

                return $result;
            }
        }

        return new ExecutionResult();
    }
}
