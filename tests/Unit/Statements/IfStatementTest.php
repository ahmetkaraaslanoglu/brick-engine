<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\IfStatement;
use IsaEken\BrickEngine\Value;

test('can parse if statement without else', function () {
    $engine = new BrickEngine(new Context([
        'x' => Value::from(10),
    ]));
    $content = 'if (x > 5) { result = true; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(IfStatement::class)
        ->and($engine->context->variables['result']->data)
        ->toBeTrue();
});

test('can parse if statement with else', function () {
    $engine = new BrickEngine(new Context([
        'x' => Value::from(4),
    ]));
    $content = 'if (x > 5) { result = true; } else { result = false; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(IfStatement::class)
        ->and($engine->context->variables['result']->data)
        ->toBeFalse();
});

test('can parse if statement with complex condition', function () {
    $engine = new BrickEngine(new Context([
        'x' => Value::from(10),
        'y' => Value::from(5),
    ]));
    $content = 'if (x > 5 && y < 10) { result = true; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(IfStatement::class)
        ->and($engine->context->variables['result']->data)
        ->toBeTrue();
});

test('can parse nested if statements', function () {
    $engine = new BrickEngine(new Context([
        'x' => Value::from(10),
        'y' => Value::from(5),
    ]));
    $content = 'if (x > 5) { if (y < 10) { result = true; } }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(IfStatement::class)
        ->and($engine->context->variables['result']->data)
        ->toBeTrue();
});
