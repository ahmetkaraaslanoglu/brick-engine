<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\ArrayKeyNotFoundException;
use IsaEken\BrickEngine\Exceptions\VariableNotFoundException;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ArrayAccessExpression extends Node implements ExpressionInterface
{
    public function __construct(ExpressionInterface $left, ExpressionInterface|null $right = null)
    {
        parent::__construct([
            'type' => 'ARRAY_ACCESS',
            'left' => $left,
            'right' => $right,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        if ($this->left::class !== IdentifierExpression::class) {
            $array = $this->left->run($runtime, $context);
        } else {
            $identifier = $this->left->value;
            if (! array_key_exists($identifier, $context->variables)) {
                throw new VariableNotFoundException($identifier);
            }

            $array = $context->variables[$identifier]->data;
        }

        $this->assertType($this->right, [IdentifierExpression::class, LiteralExpression::class]);
        $key = $context->value($this->right->run($runtime, $context))?->data ?? 0;

        if ($array instanceof Value) {
            if (array_key_exists($key, $array->data ?? [])) {
                $array = $array->data;
            }
        }

        if (array_key_exists($key, $array ?? [])) {
            return $array[$key];
        }

        return new Value($context, ValueType::Null);
    }

    public function compile(): string
    {
        $identifier = $this->identifier->value;
        $index = $this->index->value;

        return "{$identifier}[$index]";
    }
}
