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
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](new Context()))
        ->toBeInstanceOf(ExecutionResult::class);
});

test('can parse function with single parameter', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine();
    $content = 'function test(x) { return x + 1; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](new Context(arguments: [
            Value::from($emptyContext, 40),
        ]))->value->data)
        ->toBe(41);
});

test('can parse function with multiple parameters', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine();
    $content = 'function test(x, y, z) { return x + y + z; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](new Context(arguments: [
            Value::from($emptyContext, 10),
            Value::from($emptyContext, 20),
            Value::from($emptyContext, 30),
        ]))->value->data)
        ->toBe(60);
});

test('can parse function with default parameters', function () {
    $engine = new BrickEngine();
    $content = 'function test(x = 1, y = 2) { return x + y; }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](new Context())->value->data)
        ->toBe(3);
});

test('can parse function with complex body', function () {
    $emptyContext = new Context();
    $engine = new BrickEngine();
    $content = 'function test(x, y) { 
        if (x > y) {
            return x;
        } else {
            return y;
        }
    }';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $statement = $parser->parseStatement();

    $statement->run($engine->context);

    expect($statement)
        ->toBeInstanceOf(FunctionDeclareStatement::class)
        ->and($engine->context->functions['test'](new Context(arguments: [
            Value::from($emptyContext, 10),
            Value::from($emptyContext, 20),
        ]))->value->data)
        ->toBe(20)
        ->and($engine->context->functions['test'](new Context(arguments: [
                Value::from($emptyContext, 20),
                Value::from($emptyContext, 10),
        ]))->value->data)
        ->toBe(20);
});
