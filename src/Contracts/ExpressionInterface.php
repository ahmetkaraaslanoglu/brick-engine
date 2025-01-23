<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

interface ExpressionInterface extends NodeInterface
{
    public function run(Context $context): Value;
}
