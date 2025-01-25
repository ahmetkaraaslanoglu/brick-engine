<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class ObjectLiteralExpression extends Node implements ExpressionInterface
{
    public function __construct(array $elements)
    {
        parent::__construct([
            'type' => 'OBJECT_LITERAL',
            'elements' => $elements,
        ]);
    }

    public function run(Context $context): Value
    {
        $object = array_map(function ($value) use ($context) {
            return $context->value($value->run($context));
        }, $this->elements);

        return new Value(ValueType::Object, $object);
    }
}
