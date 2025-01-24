<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\ForeachStatement;
use IsaEken\BrickEngine\Value;

test('can parse foreach with value only', function () {
    $engine = new BrickEngine(new Context([
        'arr' => new Value(ValueType::Array, [
            new Value(ValueType::ArrayElement, new Value(ValueType::Numeric, 1)),
            new Value(ValueType::ArrayElement, new Value(ValueType::Numeric, 2)),
            new Value(ValueType::ArrayElement, new Value(ValueType::Numeric, 3)),
        ]),
    ]));
    $content = 'foreach (arr as value) { x = value; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ForeachStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(3);
})->skip(message: 'Feature not completed yet');

test('can parse foreach with key and value', function () {
    $engine = new BrickEngine();
    $content = 'foreach (arr as key => value) { x = value; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    expect($statement)->toBeInstanceOf(ForeachStatement::class);
})->skip(message: 'Feature not completed yet');

test('can parse foreach with multiple statements', function () {
    $engine = new BrickEngine();
    $content = 'foreach (arr as value) { 
        x = value;
        y = x + 1;
        z = y + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    expect($statement)->toBeInstanceOf(ForeachStatement::class);
})->skip(message: 'Feature not completed yet');

test('can parse nested foreach loops', function () {
    $engine = new BrickEngine();
    $content = 'foreach (arr1 as value1) { 
        foreach (arr2 as value2) {
            x = value1 + value2;
        }
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    expect($statement)->toBeInstanceOf(ForeachStatement::class);
})->skip(message: 'Feature not completed yet');
