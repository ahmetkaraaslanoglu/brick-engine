<?php

namespace IsaEken\BrickEngine\Exceptions;

use Exception;

class VariableNotFoundException extends Exception
{
    public function __construct(string $identifier)
    {
        parent::__construct("Variable '{$identifier}' not found.");
    }
}
