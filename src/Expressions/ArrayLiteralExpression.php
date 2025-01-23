<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\VariableNotFoundException;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ArrayLiteralExpression extends Node implements ExpressionInterface
{
    public function __construct(array $elements)
    {
        parent::__construct([
            'type' => 'ARRAY_LITERAL',
            'elements' => $elements,
        ]);
    }

    public function run(Context $context): Value
    {
        $elements = [];

        foreach ($this->elements as $element) {
            $this->assertType($element, ArrayElementExpression::class);
            $element = $element->run($context)->data;

            if ($element['spread']) {
                if (! array_key_exists($element['value']->data, $context->variables)) {
                    throw new VariableNotFoundException($element['value']->data);
                }

                foreach ($context->variables[$element['value']->data] as $key => $value) {
                    $elements[$key] = $value;
                }
            } else {
                if (!is_null($element['key']?->data)) {
                    $elements[$element['key']->data] = $element['value'];
                } else {
                    $elements[] = $element['value'];
                }
            }
        }

        return new Value(ValueType::Array, $elements);
    }
}
