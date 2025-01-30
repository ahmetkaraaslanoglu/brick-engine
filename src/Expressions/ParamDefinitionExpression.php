<?php

namespace IsaEken\BrickEngine\Expressions;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Node;
use IsaEken\BrickEngine\Value;

class ParamDefinitionExpression extends Node implements ExpressionInterface
{
    public function __construct(string $identifier, StatementInterface|null $defaultValue)
    {
        parent::__construct([
            'type' => 'FUNCTION_ARGUMENT',
            'identifier' => $identifier,
            'default_value' => $defaultValue,
        ]);
    }

    public function run(Runtime $runtime, Context $context): Value
    {
        parent::run($runtime, $context);
    }

    public function compile(): string
    {
        if ($this->default_value) {
            $defaultValue = $this->default_value->compile();
            return "\${$this->identifier} = {$defaultValue}";
        }

        return "\${$this->identifier}";
    }
}
