<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Contracts\NodeInterface;
use IsaEken\BrickEngine\Exceptions\InternalCriticalException;
use IsaEken\BrickEngine\Runtime\Context;

abstract class Node implements NodeInterface
{
    public array $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get(string $name): mixed
    {
        return $this->data[$name];
    }

    public function set(string $name, mixed $value): static
    {
        $this->data[$name] = $value;
        return $this;
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return $this->{$name};
    }

    public function assertType(mixed $object, string|array $class): true
    {
        if (is_array($class)) {
            $success = false;
            foreach ($class as $item) {
                if ($object instanceof $item) {
                    $success = true;
                    break;
                }
            }

            if (! $success) {
                throw new InternalCriticalException("Key is not instance of " . implode(', ', $class));
            }

            return true;
        }

        if (! $object instanceof $class) {
            throw new InternalCriticalException("Key is not instance of {$class}");
        }

        return true;
    }

    public function run(Runtime $runtime, Context $context): ExecutionResult|Value
    {
        $runtime->ticks++;

        return new ExecutionResult();
    }
}
