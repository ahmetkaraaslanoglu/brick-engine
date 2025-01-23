<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Runtime\Context;

class BrickEngine
{
    public function __construct(
        public Context $context = new Context,
    ) {
        // ...
    }

    public function run(string $content): ExecutionResult
    {
        $lexer = new Lexer($this, $content);
        $tokens = $lexer->run();

        $parser = new Parser($tokens, $content);
        $ast = $parser->parseProgram();

        $runtime = new Runtime($ast);
        $runtime->context = $this->context;

        return $runtime->run();
    }
}
