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
        $arguments = array_map(fn ($argument) => $argument->run($context), $this->arguments);
        $arguments = array_map(fn ($argument) => fromValue($argument), $arguments);

        if (! array_key_exists($callee, $context->functions)) {
            throw new FunctionNotFoundException($callee);
        }

        $value = (clone $context)->functions[$callee](...$arguments);

        if ($value) {
            return \value($value);
        }

        return new Value($context, ValueType::Void);
    }

    public function compile(): string
    {
        $callee = $this->callee;
        $arguments = array_map(fn ($argument) => $argument->compile(), $this->arguments);
        $arguments = implode(', ', $arguments);

        return "$callee($arguments)";
    }
}
