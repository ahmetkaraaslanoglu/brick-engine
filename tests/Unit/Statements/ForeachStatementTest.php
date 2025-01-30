<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\ForeachStatement;
use IsaEken\BrickEngine\Value;

test('can be compile to php', function () {
    $content = 'foreach (arr as value) { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseStatement()->compile())
        ->toBe('foreach ($arr as $value) {}');
});

test('can parse foreach with value only', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('arr', [1,2,3]);

    $content = 'foreach (arr as value) { x = value; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ForeachStatement::class)
        ->and($engine->context->value($engine->context->variables['x'])->data)
        ->toBe(3);
});

test('can parse foreach with key and value', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('arr', [
        'foo' => 'bar',
        'baz' => 'qux',
    ]);

    $content = 'foreach (arr as key => value) { x = value; y = key; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ForeachStatement::class)
        ->and($engine->context->value($engine->context->variables['x'])->data)
        ->toBe('qux')
        ->and($engine->context->value($engine->context->variables['y'])->data)
        ->toBe('baz');
});

test('can parse foreach with multiple statements', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('arr', [1,2,3]);

    $content = 'foreach (arr as value) { 
        x = value + 1;
        y = value + 1;
        z = value + 1;
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ForeachStatement::class)
        ->and($engine->context->value($engine->context->variables['x'])->data)
        ->toBe(4)
        ->and($engine->context->value($engine->context->variables['y'])->data)
        ->toBe(4)
        ->and($engine->context->value($engine->context->variables['z'])->data)
        ->toBe(4);
});

test('can parse nested foreach loops', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine(new Context(variables: [
        'arr1' => Value::from($emptyContext, [1, 2, 3]),
        'arr2' => Value::from($emptyContext, [4, 5, 6]),
    ]));
    $content = 'foreach (arr1 as value1) { 
        foreach (arr2 as value2) {
            x = value1 + value2;
        }
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(ForeachStatement::class)
        ->and($engine->context->value($engine->context->variables['x'])->data)
        ->toBe(9);
});
