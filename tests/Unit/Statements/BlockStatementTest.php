<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Statements\BlockStatement;

test('can be compile to php', function () {
    $content = '{}';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseBlock()->compile())->toBe('{}');
});

test('can parse empty block', function () {
    $engine = new BrickEngine();
    $content = '{}';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseBlock();

    expect($statement)->toBeInstanceOf(BlockStatement::class);
});

test('can parse block with single statement', function () {
    $engine = new BrickEngine();
    $content = '{ x = 42; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseBlock();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(BlockStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(42);
});

test('can parse block with multiple statements', function () {
    $engine = new BrickEngine();
    $content = '{
        x = 42;
        y = "test";
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseBlock();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(BlockStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(42)
        ->and($engine->context->variables['y']->data)
        ->toBe('test');
});

test('can parse nested blocks', function () {
    $engine = new BrickEngine();
    $content = '{
        x = 42;
        {
            y = "test";
        }
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseBlock();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(BlockStatement::class)
        ->and($engine->context->variables['x']->data)
        ->toBe(42)
        ->and($engine->context->variables['y']->data)
        ->toBe('test');
});
