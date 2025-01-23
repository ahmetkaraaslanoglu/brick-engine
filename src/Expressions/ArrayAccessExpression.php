<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ArrayAccessExpression extends Node implements ExpressionInterface
{
    public function __construct(ExpressionInterface $identifier, ExpressionInterface|null $index)
    {
        parent::__construct([
            'type' => 'ARRAY_ACCESS',
            'identifier' => $identifier,
            'index' => $index,
        ]);
    }

    public function run(Context $context): Value
    {
        $this->assertType($this->identifier, IdentifierExpression::class);
        $this->assertType($this->index, [IdentifierExpression::class, LiteralExpression::class]);

        if (! array_key_exists($this->identifier->value, $context->variables)) {
            throw new \Exception("Variable not found: {$this->identifier->value}");
        }

        $index = $this->index ? $this->index->run($context)?->data : null;
        $array = $context->variables[$this->identifier->data['value']]->data;

        if (array_key_exists($index, $array)) {
            return $array[$index];
        }

        // @todo throw exception

        return new Value(ValueType::Null);
    }
}
