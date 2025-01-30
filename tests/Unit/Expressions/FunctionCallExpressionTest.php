<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\FunctionCallExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse function call without arguments', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn () => Value::from($emptyContext, 42),
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
        'test' => fn (Context $context) => $context->value($context->arguments[0]),
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
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context(functions: [
        'test' => function (Context $context) {
            $arg1 = $context->value($context->arguments[0])->data;
            $arg2 = $context->value($context->arguments[1])->data;
            $arg3 = $context->value($context->arguments[2])->data;
            return Value::from($context, $arg1 . $arg2 . $arg3);
        },
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
    $engine = new BrickEngine(new Context(functions: [
        'test' => function ($context) {
            $arg1 = $context->value($context->arguments[0])->data;
            $arg2 = $context->value($context->arguments[1])->data;
            return \value($arg1 + $arg2);
        },
    ]));
    $engine->context
        ->setVariable('a', 10)
        ->setVariable('b', 20)
        ->setVariable('x', 5)
        ->setVariable('y', 2);

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
