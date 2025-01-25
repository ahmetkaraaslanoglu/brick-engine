<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\FunctionCallExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse function call without arguments', function () {
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn () => Value::from(42),
    ]));
    $content = 'test()';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse function call with single argument', function () {
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn ($a) => $a,
    ]));
    $content = 'test(42)';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse function call with multiple arguments', function () {
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn ($a, $b, $c) => Value::from($a->data . $b->data . $c->data),
    ]));
    $content = 'test(1, "two", true)';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe('1two1');
});

test('can parse function call with expression arguments', function () {
    $engine = new BrickEngine(new Context([
        'a' => Value::from(10),
        'b' => Value::from(20),
        'x' => Value::from(5),
        'y' => Value::from(2),
    ], [
        'test' => fn ($a, $b) => new Value(ValueType::Numeric, $a->data + $b->data),
    ]));
    $content = 'test(a + b, x > y)';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(31);
});
