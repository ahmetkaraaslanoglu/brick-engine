<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\ClosureExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can be compile to php', function () {
    $contents = [
        [
            'function test() {}',
            'function test() {}',
        ],
        [
            'function test(x) {}',
            'function test($x) {}'
        ],
        [
            'function test(x, y) {}',
            'function test($x, $y) {}'
        ],
        [
            'function test(x = 1, y = 2) {}',
            'function test($x = 1, $y = 2) {}'
        ],
        [
            'function test() { x = 2; }',
            'function test() {$x = 2;}'
        ],
    ];

    foreach ($contents as $content) {
        $parser = new Parser(new Lexer(new BrickEngine(), $content[0])->run(), $content[0]);
        expect($parser->parseExpression()->compile())->toBe($content[1]);
    }
});

test('can parse function without parameters', function () {
    $content = 'function test() { return 42; }';

    $engine = new BrickEngine();
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseExpression();
    $statement->run($engine->context);
    expect($statement)
        ->toBeInstanceOf(ClosureExpression::class)
        ->and(($engine->context->variables['test']->data)())
        ->toBe(42);
});

test('can parse function with single parameter', function () {
    $content = 'function test(x) { return x + 1; }';

    $engine = new BrickEngine();
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseExpression();
    $statement->run($engine->context);
    expect($statement)
        ->toBeInstanceOf(ClosureExpression::class)
        ->and(($engine->context->variables['test']->data)(40))
        ->toBe(41);
});

test('can parse function with multiple parameters', function () {
    $content = 'function test(x, y, z) { return x + y + z; }';

    $engine = new BrickEngine();
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseExpression();
    $statement->run($engine->context);
    expect($statement)
        ->toBeInstanceOf(ClosureExpression::class)
        ->and(($engine->context->variables['test']->data)(10, 20, 30))
        ->toBe(60);
});

test('can parse function with default parameters', function () {
    $content = 'function test(x = 1, y = 2) { return x + y; }';

    $engine = new BrickEngine();
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseExpression();
    $statement->run($engine->context);
    expect($statement)
        ->toBeInstanceOf(ClosureExpression::class)
        ->and(($engine->context->variables['test']->data)())
        ->toBe(3);
});

test('can parse function with complex body', function () {
    $content = 'function test(x, y) {
        if (x > y) {
            return x;
        } else {
            return y;
        }
    }';

    $engine = new BrickEngine();
    $statement = new Parser(new Lexer($engine, $content)->run(), $content)->parseExpression();
    $statement->run($engine->context);
    expect($statement)
        ->toBeInstanceOf(ClosureExpression::class)
        ->and(($engine->context->variables['test']->data)(10, 20))
        ->toBe(20);
});
