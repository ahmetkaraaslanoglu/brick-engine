<?php

namespace IsaEken\BrickEngine;

class ExecutionResult
{
    public function __construct(
        public ?Value $value = null,
        public bool $return = false,
        public bool $break = false,
        public bool $continue = false
    ) {
        // ...
    }
}
