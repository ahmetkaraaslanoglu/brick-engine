<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\InvalidLeftSideTargetException;
use IsaEken\BrickEngine\Exceptions\UnsupportedException;
use IsaEken\BrickEngine\Exceptions\VariableNotFoundException;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class UnaryExpression extends Node implements ExpressionInterface
{
    public function __construct(string $operator, ExpressionInterface $left)
    {
        parent::__construct([
            'type' => 'UNARY',
            'operator' => $operator,
            'left' => $left,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        $operator = $this->operator;

        $left = $this->resolveValue($context, 'left');
        $right = $this->resolveValue($context, 'right');

        if ($operator === '+' && ($left->is(ValueType::String) || $right->is(ValueType::String))) {
            return new Value($context, ValueType::String, $left->data . $right->data);
        }

        if ($operator == '+=') {
            if (! $this->left instanceof IdentifierExpression) {
                throw new InvalidLeftSideTargetException();
            }

            if (! array_key_exists($this->left->value, $context->variables)) {
                throw new VariableNotFoundException($this->left->value);
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
            return $this->calculate($context, $left, $right, $operator);
        }

        if (in_array($operator, ['&&', '||', '==', '!=', '>', '<', '>=', '<=', '===', '!==', '??', '?:'])) {
            return $this->compare($context, $left, $right, $operator);
        }

        throw new UnsupportedException("Unsupported operator: {$operator}");
    }

    private function calculate(Context $context, Value $left, Value $right, string $operator): Value
    {
        return new Value($context, ValueType::Numeric, match ($operator) {
            '+' => \fromValue($left) + \fromValue($right),
            '-' => $left->data - $right->data,
            '*' => $left->data * $right->data,
            '/' => $left->data / $right->data,
            '%' => $left->data % $right->data,
            default => throw new UnsupportedException("Unsupported operator: {$operator}"),
        });
    }

    private function compare(Context $context, Value $left, Value $right, string $operator): Value
    {
        return new Value($context, ValueType::Boolean, match ($operator) {
            '&&' => \fromValue($left) && \fromValue($right),
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
            default => throw new UnsupportedException("Unsupported operator: {$operator}"),
        });
    }

    private function resolveValue(Context $context, string $side): Value
    {
        $object = $this->{$side};
        if ($object instanceof IdentifierExpression) {
            $identifier = $object->value;
            if (! array_key_exists($identifier, $context->variables)) {
                throw new VariableNotFoundException($identifier);
            }

            return $context->variables[$identifier];
        }

        return $object->run($context);
    }

    public function compile(): string
    {
        return $this->left->compile().$this->operator;
    }
}
