<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Runtime\Context;

interface StatementInterface extends NodeInterface
{
    public function run(Context $context): ExecutionResult;
}
