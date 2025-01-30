<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\FunctionCallExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can be compile to php', function () {
    $content = 'test()';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())
        ->toBe('test()');

    $content = 'test(1, 2, 3)';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())
        ->toBe('test(1, 2, 3)');
});

test('can parse function call without arguments', function () {
    $engine = new BrickEngine();
    $engine->context->setFunction('test', fn () => 42);
    $content = 'test()';
    $expression = new Parser(new Lexer($engine, $content)->run(), $content)->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse function call with single argument', function () {
    $engine = new BrickEngine();
    $engine->context->setFunction('test', fn (...$args) => $args[0]);

    $content = 'test(42)';
    $expression = new Parser(new Lexer($engine, $content)->run(), $content)
        ->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse function call with multiple arguments', function () {
    $engine = new BrickEngine();
    $engine->context->setFunction('test', fn (...$args) => array_reduce($args, fn ($carry, $item) => $carry . $item, ''));
    $content = 'test(1, "two", true)';
    $expression = new Parser(new Lexer($engine, $content)->run(), $content)->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe('1two1');
});

test('can parse function call with expression arguments', function () {
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn ($arg1, $arg2) => $arg1 + $arg2,
    ]));
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20)
        ->setVariable('x', 5)
        ->setVariable('y', 2);
    $content = 'test(a + b, x > y)';

    $expression = new Parser(new Lexer($engine, $content)->run(), $content)->parseFactor();

    expect($expression)
        ->toBeInstanceOf(FunctionCallExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(31);
});
