<?php

namespace IsaEken\BrickEngine\Contracts;

use IsaEken\BrickEngine\BrickEngine;

interface ExtensionInterface
{
    public function __construct(BrickEngine $engine);

    public function register(): void;
}
