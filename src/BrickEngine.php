<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Extensions\HttpExtension;
use IsaEken\BrickEngine\Extensions\VarDumperExtension;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Runtime\Context;

class BrickEngine
{
    public const array EXTENSIONS = [
        HttpExtension::class,
        VarDumperExtension::class,
    ];

    public function __construct(public Context $context = new Context)
    {
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

        foreach (self::EXTENSIONS as $extension) {
            new $extension($this)->register();
        }

        return $runtime->run();
    }
}
