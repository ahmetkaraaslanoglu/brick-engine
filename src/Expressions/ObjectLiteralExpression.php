<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Runtime;
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

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);

        $object = array_map(function ($value) use ($context, $runtime) {
            return $context->value($value->run($runtime, $context));
        }, $this->elements);

        return new Value($context, ValueType::Object, $object);
    }

    public function compile(): string
    {
        $elements = array_map(fn ($element) => $element->compile(), $this->elements);
        $element = implode(', ', $elements);

        return "(object) [$element]";
    }
}
