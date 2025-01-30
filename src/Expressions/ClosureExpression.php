<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class ClosureExpression extends Node implements ExpressionInterface
{
    public function __construct(string|null $callee, array $arguments, StatementInterface $body)
    {
        parent::__construct([
            'type' => 'CLOSURE_EXPRESSION',
            'callee' => $callee,
            'arguments' => $arguments,
            'body' => $body,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        $closure = function (...$arguments) use ($runtime, $context) {
            foreach ($this->arguments as $index => $argument) {
                if ($arguments[$index] ?? false) {
                    $context->variables[$argument->identifier] = value($arguments[$index]);
                } else {
                    $context->variables[$argument->identifier] = $argument->default_value?->run($runtime, $context) ?? value(null);
                }
            }

            $result = $this->body->run($runtime, $context);
            if ($result->return) {
                return fromValue($result->value);
            }
        };

        $closure = new Value($context, ValueType::Closure, $closure);
        if ($this->callee) {
            $context->variables[$this->callee] = $closure;
        }

        return $closure;
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
