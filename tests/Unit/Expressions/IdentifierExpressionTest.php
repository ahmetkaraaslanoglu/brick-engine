<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\IdentifierExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse simple identifier', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context([
        'variable' => Value::from($emptyContext, 42),
    ]));
    $content = 'variable';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(IdentifierExpression::class)
        ->and($engine->context->value($expression->run($engine->context))->data)
        ->toBe(42);
});

test('can parse identifier with underscore', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context([
        'my_variable' => Value::from($emptyContext, 42),
    ]));
    $content = 'my_variable';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(IdentifierExpression::class)
        ->and($engine->context->value($expression->run($engine->context))->data)
        ->toBe(42);
});

test('can parse identifier with numbers', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context([
        'variable123' => Value::from($emptyContext, 42),
    ]));
    $content = 'variable123';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(IdentifierExpression::class)
        ->and($engine->context->value($expression->run($engine->context))->data)
        ->toBe(42);
});
