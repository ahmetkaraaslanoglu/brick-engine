<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
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

    public function run(Runtime $runtime, Context $context): ExecutionResult
    {
        parent::run($runtime, $context);

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

            if ($result->break) {
                $result->break = true;
                return $result;
            }
        }

        return new ExecutionResult();
    }

    public function compile(): string
    {
        $statements = array_map(fn ($statement) => $statement->compile(), $this->data['statements']);
        $statements = implode("\n", $statements);

        return "{".$statements."}";
    }
}
