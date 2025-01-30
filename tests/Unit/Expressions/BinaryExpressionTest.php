<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\BinaryExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can be compile to php', function () {
    $content = 'a + b';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseExpression()->compile())->toBe('$a + $b');

    $content = 'a + 15 + c';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseExpression()->compile())->toBe('$a + 15 + $c');
});

test('can parse addition', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20);

    $content = 'a + b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(30);
});

test('can parse subtraction', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20);

    $content = 'a - b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(-10);
});

test('can parse comparison', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20);

    $content = 'a > b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeFalse();
});

test('can parse equality', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20);

    $content = 'a == b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeFalse();
});

test('can parse logical operators', function () {
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('a', true)
        ->setVariable('b', false);

    $content = 'a && b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeFalse();

    $content = 'a || b';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeTrue();
});

test('can parse left is variable', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('var', 10);

    $content = 'var + 1';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(11);
});

test('can parse right is variable', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('var', 10);

    $content = '1 + var';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseExpression();

    expect($expression)
        ->toBeInstanceOf(BinaryExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(11);
});
