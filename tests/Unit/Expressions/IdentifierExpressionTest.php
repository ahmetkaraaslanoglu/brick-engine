<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\IdentifierExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse simple identifier', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('variable', 42);

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
    $engine = new BrickEngine();
    $engine->context->setVariable('my_variable', 42);

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
    $engine = new BrickEngine();
    $engine->context->setVariable('variable123', 42);

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
