<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\Exceptions\IgnorableException;
use IsaEken\BrickEngine\Runtime\Context;

class Runtime
{
    public function __construct(
        public StatementInterface $program,
        public Context $context = new Context,
    ) {
        //
    }

    public function run(): ExecutionResult
    {
        try {
            return $this->program->run(
                $this->context,
            );
        } catch (IgnorableException $ignorableException) {
            dump($ignorableException);
        }
    }
}
