<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

interface NodeInterface
{
    public function run(Runtime $runtime, Context $context): ExecutionResult|Value;

    public function compile(): string;
}
