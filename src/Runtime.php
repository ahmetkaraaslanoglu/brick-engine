<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\Exceptions\IgnorableException;
use IsaEken\BrickEngine\Runtime\Context;

class Runtime
{
    public int $ticks = 0;

    public int $tickLimit = 10_000;

    public function __construct(
        public StatementInterface $program,
        public Context $context = new Context,
    ) {
        //
    }

    public function run(): ExecutionResult
    {
        try {
            $a = $this->program->run(
                $this,
                $this->context,
            );
            dd($a, $this->ticks);
        } catch (IgnorableException $ignorableException) {
            dump($ignorableException);
        }
    }
}
