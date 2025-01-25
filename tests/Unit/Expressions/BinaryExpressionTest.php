<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\BinaryExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse addition', function () {
    $engine = new BrickEngine(new Context([
        'a' => Value::from(10),
        'b' => Value::from(20),
    ]));
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
    $engine = new BrickEngine(new Context([
        'a' => Value::from(10),
        'b' => Value::from(20),
    ]));
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
    $engine = new BrickEngine(new Context([
        'a' => Value::from(10),
        'b' => Value::from(20),
    ]));
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
    $engine = new BrickEngine(new Context([
        'a' => Value::from(10),
        'b' => Value::from(20),
    ]));
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
    $engine = new BrickEngine(new Context([
        'a' => Value::from(true),
        'b' => Value::from(false),
    ]));
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
    $engine = new BrickEngine(new Context([
        'var' => Value::from(10),
    ]));
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
    $engine = new BrickEngine(new Context([
        'var' => Value::from(10),
    ]));
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
