<?php

namespace IsaEken\BrickEngine\Runtime;

use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Value;

class Context
{
    public array $variables = [];

    public array $functions = [];

    public function __construct(array $variables = [], array $functions = [])
    {
        $this->variables = $variables;
        $this->functions = $functions;
    }

    public function value(Value $value): mixed
    {
        if ($value->is(ValueType::Identifier)) {
            $identifier = $value->data;
            if (! array_key_exists($identifier, $this->variables)) {
                throw new \Exception("Variable not found: {$identifier}");
            }

            return $this->variables[$identifier];
        }

        return $value;
    }
}
