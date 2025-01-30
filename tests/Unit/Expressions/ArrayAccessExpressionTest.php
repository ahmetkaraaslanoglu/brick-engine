<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\ArrayAccessExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can be compile to php', function () {
    $content = 'arr[]';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseIdentifierOrArrayAccess()->compile())
        ->toBe('arr[]');
});

test('can parse array access with numeric index', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('arr', [42]);

    $content = 'arr[0]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseIdentifierOrArrayAccess();

    expect($expression)
        ->toBeInstanceOf(ArrayAccessExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse array access with index', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('arr', [10, 20, 30]);

    $content = 'arr[1]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseIdentifierOrArrayAccess();

    expect($expression)
        ->toBeInstanceOf(ArrayAccessExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(20);
});
