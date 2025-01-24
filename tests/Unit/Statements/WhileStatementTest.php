<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\WhileStatement;
use IsaEken\BrickEngine\Value;

test('can parse basic while loop', function () {
    $engine = new BrickEngine(new Context([
        'x' => new Value(ValueType::Numeric, 0),
    ]));
    $content = 'while (x < 10) { x = x + 1; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10);
});

test('can parse while loop with complex condition', function () {
    $engine = new BrickEngine(new Context([
        'x' => new Value(ValueType::Numeric, 0),
        'y' => new Value(ValueType::Numeric, 10),
    ]));
    $content = 'while (x < 10 && y > 0) { x = x + 1; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10);
});

test('can parse while loop with multiple statements', function () {
    $engine = new BrickEngine(new Context([
        'x' => new Value(ValueType::Numeric, 0),
        'y' => new Value(ValueType::Numeric, 0),
        'z' => new Value(ValueType::Numeric, 0),
    ]));
    $content = 'while (x < 10) { 
        x = x + 1;
        y = x + 2;
        z = y + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10)
        ->and($engine->context->variables['y']->data)
        ->toBe(12)
        ->and($engine->context->variables['z']->data)
        ->toBe(13);
});

/**
 * @todo Implement nested while loops
 */
test('can parse nested while loops', function () {
    $engine = new BrickEngine(new Context([
        'x' => new Value(ValueType::Numeric, 0),
        'y' => new Value(ValueType::Numeric, 0),
    ]));
    $content = 'while (x < 10) { 
        while (y < 5) {
            y = y + 1;
        }
        x = x + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    expect($statement)
        ->toBeInstanceOf(WhileStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(10)
        ->and($engine->context->variables['y']->data)
        ->toBe(5);
})->skip(message: 'Feature not completed yet');
