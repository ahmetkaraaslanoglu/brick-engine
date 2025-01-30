<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\ExecutionResult;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Statements\FunctionDeclareStatement;
use IsaEken\BrickEngine\Value;

test('can be compile to php', function () {
    $content = 'function test() { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseStatement()->compile())
        ->toBe('function test() {}');

    $content = 'function test(x) { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('function test($x) {}');

    $content = 'function test(x, y) { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('function test($x, $y) {}');

    $content = 'function test(x = 1, y = 2) { }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('function test($x = 1, $y = 2) {}');

    $content = 'function test() { x = 2; }';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);
    expect($parser->parseStatement()->compile())
        ->toBe('function test() {$x = 2;}');
});

test('can parse function without parameters', function () {
    $engine = new BrickEngine();
    $content = 'function test() { return 42; }';
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test']())
        ->toBe(42);
});

test('can parse function with single parameter', function () {
    $engine = new BrickEngine();
    $content = 'function test(x) { return x + 1; }';

    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](40))
        ->toBe(41);
});

test('can parse function with multiple parameters', function () {
    $engine = new BrickEngine();
    $content = 'function test(x, y, z) { return x + y + z; }';

    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](10, 20, 30))
        ->toBe(60);
});

test('can parse function with default parameters', function () {
    $engine = new BrickEngine();
    $content = 'function test(x = 1, y = 2) { return x + y; }';

    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test']())
        ->toBe(3);
});

test('can parse function with complex body', function () {
    $engine = new BrickEngine();
    $content = 'function test(x, y) { 
        if (x > y) {
            return x;
        } else {
            return y;
        }
    }';

    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseStatement();
    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](10, 20))
        ->toBe(20);
});
