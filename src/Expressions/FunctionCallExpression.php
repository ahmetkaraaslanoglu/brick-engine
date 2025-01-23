<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\FunctionNotFoundException;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class FunctionCallExpression extends Node implements ExpressionInterface
{
    public function __construct(string $callee, array $arguments)
    {
        parent::__construct([
            'type' => 'FUNCTION_CALL',
            'callee' => $callee,
            'arguments' => $arguments,
        ]);
    }

    public function run(Context $context): Value
    {
        $callee = $this->callee;
        $arguments = $this->arguments;

        foreach ($arguments as $index => $argument) {
            $arguments[$index] = $argument->run($context);
        }

        if (! array_key_exists($callee, $context->functions)) {
            throw new FunctionNotFoundException($callee);
        }

        $value = $context->functions[$callee](...$arguments);
        if ($value instanceof ExecutionResult) {
            return $value->value ?? new Value(ValueType::Void);
        }

        return $value;
    }
}
