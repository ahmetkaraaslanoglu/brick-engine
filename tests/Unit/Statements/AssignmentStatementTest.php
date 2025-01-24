<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\AssignmentStatement;
use IsaEken\BrickEngine\Value;

test('can parse simple assignment', function () {
    $engine = new BrickEngine();
    $content = 'x = 42;';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(AssignmentStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(42);
});

test('can parse assignment with expression', function () {
    $engine = new BrickEngine(new Context([
        'a' => new Value(ValueType::Numeric, 10),
        'b' => new Value(ValueType::Numeric, 20),
    ]));
    $content = 'x = a + b;';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(AssignmentStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(30);
});

test('can parse assignment with string', function () {
    $engine = new BrickEngine();
    $content = 'message = "Hello World";';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(AssignmentStatement::class)
        ->and($engine->context->variables['message']->data)
        ->toBe('Hello World');
});

test('can parse assignment with array', function () {
    $engine = new BrickEngine();
    $content = 'numbers = [1, 2, 3];';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(AssignmentStatement::class)
        ->and($engine->context->variables['numbers']->data[0]->data)
        ->toBe(1)
        ->and($engine->context->variables['numbers']->data[1]->data)
        ->toBe(2)
        ->and($engine->context->variables['numbers']->data[2]->data)
        ->toBe(3);
});

test('can parse assignment with function call', function () {
    $engine = new BrickEngine(new Context(functions: [
        'test' => fn () => new Value(ValueType::Numeric, 42),
    ]));
    $content = 'result = test();';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(AssignmentStatement::class)
        ->and($engine->context->variables['result']->data)
        ->toBe(42);
});
