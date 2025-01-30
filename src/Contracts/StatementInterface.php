<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;

interface StatementInterface extends NodeInterface
{
    public function run(Runtime $runtime, Context $context): ExecutionResult;
}
