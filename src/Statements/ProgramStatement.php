<?php

namespace IsaEken\BrickEngine\Statements;

use IsaEken\BrickEngine\Contracts\StatementInterface;

class ProgramStatement extends BlockStatement implements StatementInterface
{
    public function __construct(array $statements)
    {
        parent::__construct($statements);
        $this->data['type'] = 'PROGRAM';
    }
}
