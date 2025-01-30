<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;

class FunctionDeclareStatement extends Node implements StatementInterface
{
    public function __construct(string $callee, array $arguments, StatementInterface $body)
    {
        parent::__construct([
            'type' => 'FUNCTION_DECLARE',
            'callee' => $callee,
            'arguments' => $arguments,
            'body' => $body,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $context->functions[$this->callee] = function (Context $context) {
            $arguments = array_map(function ($argument) use ($context) {
                return $context->value($argument);
            }, $context->arguments);

            foreach ($this->arguments as $index => $argument) {
                if ($arguments[$index] ?? false) {
                    $context->variables[$argument->identifier] = $arguments[$index];
                    continue;
                }

                if ($argument->default_value) {
                    $context->variables[$argument->identifier] = $argument->default_value->run($context);
                } else {
                    $context->variables[$argument->identifier] = null;
                }
            }

            return $this->body->run($context);
        };

        return new ExecutionResult();
    }

    public function compile(): string
    {
        $callee = $this->callee;
        $arguments = array_map(fn ($argument) => $argument->compile(), $this->arguments);
        $arguments = implode(', ', $arguments);
        $body = $this->body->compile();

        return "function $callee($arguments) $body";
    }
}
