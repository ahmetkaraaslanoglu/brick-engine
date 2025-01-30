<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\ForeachStatement;
use IsaEken\BrickEngine\Value;

test('can be compile to php', function () {
    $content = 'for (i = 0; i < 10; i++) { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseStatement()->compile())
        ->toBe('for ($i = 0; $i < 10; $i++) { }');

    $content = 'for (i = 0; i < 10; i++) { result = true; }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('for ($i = 0; $i < 10; $i++) { $result = true; }');
});

// @todo add tests
