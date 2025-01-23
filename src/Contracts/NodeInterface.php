<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

interface NodeInterface
{
    public function run(Context $context): ExecutionResult|Value;
}
