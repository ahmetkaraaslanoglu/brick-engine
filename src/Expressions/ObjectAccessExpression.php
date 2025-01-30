<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\ArrayKeyNotFoundException;
use IsaEken\BrickEngine\Exceptions\VariableNotFoundException;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ObjectAccessExpression extends Node implements ExpressionInterface
{
    public function __construct(ExpressionInterface $identifier, string $key)
    {
        parent::__construct([
            'type' => 'OBJECT_ACCESS',
            'identifier' => $identifier,
            'key' => $key,
        ]);
    }

    public function run(Context $context): Value
    {
        $this->assertType($this->identifier, IdentifierExpression::class);

        if (array_key_exists($this->identifier->value, $context->namespaces)) {
            $key = $this->key ?? null;
            $object = $context->namespaces[$this->identifier->data['value']];
            if (array_key_exists($key, $object)) {
                return \value($object[$key]);
            }

            throw new ArrayKeyNotFoundException();
        }

        if (! array_key_exists($this->identifier->value, $context->variables)) {
            throw new VariableNotFoundException($this->identifier->value);
        }

        $key = $this->key ?? null;
        $object = $context->variables[$this->identifier->data['value']]->data;

        if (! is_array($object)) {
            throw new ArrayKeyNotFoundException();
        }

        if (array_key_exists($key, $object)) {
            return $object[$key];
        }

        // throw new ArrayKeyNotFoundException(); @todo: throw this as an warning

        return new Value($context, ValueType::Null);
    }

    public function compile(): string
    {
        $identifier = $this->identifier->value;
        $index = $this->index->value;

        return "{$identifier}[$index]";
    }
}
