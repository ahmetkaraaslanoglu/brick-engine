<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\Runtime;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

interface ExpressionInterface extends NodeInterface
{
    public function run(Runtime $runtime, Context $context): Value;
}
