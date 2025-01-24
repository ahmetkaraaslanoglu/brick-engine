<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\LiteralExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can parse numeric literal', function () {
    $engine = new BrickEngine();
    $content = '42';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse string literal', function () {
    $engine = new BrickEngine();
    $content = '"Hello World"';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe('Hello World');
});

test('can parse boolean literal', function () {
    $engine = new BrickEngine();
    $content = 'true';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeTrue();

    $content = 'false';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeFalse();
});
