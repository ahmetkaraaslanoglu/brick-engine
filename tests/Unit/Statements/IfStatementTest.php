<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Statements\IfStatement;

test('can be compile to php', function () {
    $content = 'if (x > 5) { result = true; }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseStatement()->compile())
        ->toBe('if ($x > 5) {$result = true;}');

    $content = 'if (x > 5) { result = true; } else { result = false; }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('if ($x > 5) {$result = true;} else {$result = false;}');
});

test('can parse if statement without else', function () {
    $engine = new BrickEngine();
    $engine->context->setVariable('x', 10);

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
    $engine = new BrickEngine();
    $engine->context->setVariable('x', 4);

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
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 10)
        ->setVariable('y', 5);

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
    $engine = new BrickEngine();
    $engine->context
        ->setVariable('x', 10)
        ->setVariable('y', 5);

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
