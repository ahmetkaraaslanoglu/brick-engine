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
        'a' => new Value(ValueType::Numeric, 10),
        'b' => new Value(ValueType::Numeric, 20),
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
        'a' => new Value(ValueType::Numeric, 10),
        'b' => new Value(ValueType::Numeric, 20),
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
        'a' => new Value(ValueType::Numeric, 10),
        'b' => new Value(ValueType::Numeric, 20),
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
        'a' => new Value(ValueType::Numeric, 10),
        'b' => new Value(ValueType::Numeric, 20),
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
        'a' => new Value(ValueType::Boolean, true),
        'b' => new Value(ValueType::Boolean, false),
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
