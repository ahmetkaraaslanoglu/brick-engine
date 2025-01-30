<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\FunctionNotFoundException;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class FunctionCallExpression extends Node implements ExpressionInterface
{
    /**
     * @param string $callee // @todo change this to expression
     * @param array $arguments
     */
    public function __construct(string|ExpressionInterface $callee, array $arguments)
    {
        parent::__construct([
            'type' => 'FUNCTION_CALL',
            'callee' => $callee,
            'arguments' => $arguments,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        $arguments = array_map(fn ($argument) => $argument->run($context), $this->arguments);
        $arguments = array_map(fn ($argument) => fromValue($argument), $arguments);

        $callee = $this->callee;
        $closure = null;

        if ($callee instanceof ExpressionInterface) {
            $callee = $callee->run($context);
        }

        // @todo deprecate old function calling
        if (is_string($callee) && array_key_exists($callee, $context->functions)) {
            $closure = (clone $context)->functions[$callee];
        } else if (is_string($callee) && array_key_exists($callee, $context->variables) && $context->variables[$callee]->type === ValueType::Closure) {
            $closure = ($context->variables[$callee]->data);
        } else if ($callee instanceof ExpressionInterface && $callee->type === ValueType::Closure) {
            $closure = $callee->data;
        } else if ($callee instanceof Value && $callee->type === ValueType::Closure) {
            $closure = $callee->data;
        }

        if (is_null($closure)) {
            throw new FunctionNotFoundException($callee);
        }

        $value = $closure(...$arguments);

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
