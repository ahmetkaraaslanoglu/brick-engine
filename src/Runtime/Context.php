<?php

namespace IsaEken\BrickEngine\Runtime;

use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Exceptions\VariableNotFoundException;
use IsaEken\BrickEngine\Value;

class Context
{
    public function __construct(
        public array $variables = [],
        public array $functions = [],
        public array $arguments = [], // @deprecated
    )
    {
        // ...
    }

    public function setVariable(string $identifier, mixed $value): self
    {
        $this->variables[$identifier] = value($value);

        return $this;
    }

    public function setFunction(string $callee, callable $function): self
    {
        $this->functions[$callee] = $function;

        return $this;
    }

    public function value(Value $value): mixed
    {
        if ($value->is(ValueType::Identifier)) {
            $identifier = $value->data;
            if (! array_key_exists($identifier, $this->variables)) {
                throw new VariableNotFoundException($identifier);
            }

            return $this->variables[$identifier];
        }

        return $value;
    }
}
