<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ForeachStatement extends Node implements StatementInterface
{
    public function __construct(ExpressionInterface $left, ExpressionInterface $right, StatementInterface $body)
    {
        parent::__construct([
            'type' => 'FOREACH',
            'left' => $left,
            'right' => $right,
            'body' => $body,
        ]);
    }

    public function run(Context $context): ExecutionResult
    {
        $left = $this->left->run($context);
        $right = $this->right->run($context);

        if (! $context->variables[$left->data]->is(ValueType::Array)) {
            throw new \Exception("Left side of foreach statement must be an array.");
        }

        $result = null;
        foreach ($context->variables[$left->data]->data as $key => $value) {
            if ($right->is(ValueType::ArrayElement)) {
                $keyName = $right->data['key']->data;
                $valueName = $right->data['value']->data;

                $context->variables[$keyName] = new Value(ValueType::String, $key);
            } else {
                $valueName = $right->data;
            }

            $context->variables[$valueName] = $value;
            $result = $this->body->run($context);
        }

        return $result;
    }
}
