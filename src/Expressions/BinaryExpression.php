<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class BinaryExpression extends Node implements ExpressionInterface
{
    public function __construct(string $operator, ExpressionInterface $left, ExpressionInterface $right)
    {
        parent::__construct([
            'type' => 'BINARY',
            'operator' => $operator,
            'left' => $left,
            'right' => $right,
        ]);
    }

    public function run(Context $context): Value
    {
        $operator = $this->operator;

        $left = $this->resolveValue($context, 'left');
        $right = $this->resolveValue($context, 'right');

        if ($operator === '+' && ($left->is(ValueType::String) || $right->is(ValueType::String))) {
            return new Value(ValueType::String, $left->data . $right->data);
        }

        if ($operator == '+=') {
            if (! $this->left instanceof IdentifierExpression) {
                throw new \Exception("Left side of += operator must be a variable.");
            }

            if (! array_key_exists($this->left->value, $context->variables)) {
                throw new \Exception("Variable not found: {$this->left->value}");
            }

            if ($context->variables[$this->left->value]->is(ValueType::String) || $right->is(ValueType::String)) {
                $value = $context->variables[$this->left->value]->data;
                $context->variables[$this->left->value]->data = $value . $right->data;
            } else {
                $value = $context->variables[$this->left->value];
                $value->data = $value->data + $right->data;
                $context->variables[$this->left->value] = $value;
            }

            return $context->variables[$this->left->value];
        }

        if (in_array($operator, ['+', '-', '*', '/', '%'])) {
            return $this->calculate($left, $right, $operator);
        }

        if (in_array($operator, ['&&', '||', '==', '!=', '>', '<', '>=', '<=', '===', '!==', '??', '?:'])) {
            return $this->compare($left, $right, $operator);
        }

        throw new \Exception("Unknown operator: {$operator}");
    }

    private function calculate(Value $left, Value $right, string $operator): Value
    {
        return new Value(ValueType::Numeric, match ($operator) {
            '+' => $left->data + $right->data,
            '-' => $left->data - $right->data,
            '*' => $left->data * $right->data,
            '/' => $left->data / $right->data,
            '%' => $left->data % $right->data,
            default => throw new \Exception("Unknown operator: {$operator}"),
        });
    }

    private function compare(Value $left, Value $right, string $operator): Value
    {
        return new Value(ValueType::Boolean, match ($operator) {
            '&&' => $left->data && $right->data,
            '||' => $left->data || $right->data,
            '==' => $left->data == $right->data,
            '!=' => $left->data != $right->data,
            '>' => $left->data > $right->data,
            '<' => $left->data < $right->data,
            '>=' => $left->data >= $right->data,
            '<=' => $left->data <= $right->data,
            '===' => $left->data === $right->data,
            '!==' => $left->data !== $right->data,
            '??' => $left->data ?? $right->data,
            '?:' => $left->data ?: $right->data,
            default => throw new \Exception("Unknown operator: {$operator}"),
        });
    }

    private function resolveValue(Context $context, string $side): Value
    {
        $object = $this->{$side};
        if ($object instanceof IdentifierExpression) {
            $identifier = $object->value;
            if (! array_key_exists($identifier, $context->variables)) {
                throw new \Exception("Variable not found: {$identifier}");
            }

            return $context->variables[$identifier];
        }

        return $object->run($context);
    }
}
