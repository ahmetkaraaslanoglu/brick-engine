<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\ArrayLiteralExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can be compile to php', function () {
    $content = '[]';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('[]');

    $content = '[1, 2, 3]';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('[1, 2, 3]');

    $content = '[1 => "one", 2 => "two"]';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('[1 => "one", 2 => "two"]');

    $content = '[1, "two", ...[3, 4, 5]]';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('[1, "two", ...[3, 4, 5]]');
});

test('can parse empty array', function () {
    $engine = new BrickEngine();
    $content = '[]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(ArrayLiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe([]);
});

/**
 * @todo update this test
 */
test('can parse array with numeric values', function () {
    $engine = new BrickEngine();
    $content = '[1, 2, 3]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();
    $data = $expression->run($engine->context)->data;

    expect($expression)
        ->toBeInstanceOf(ArrayLiteralExpression::class)
        ->and($data)
        ->toBe([
            $data[0],
            $data[1],
            $data[2],
        ]);
});

/**
 * @todo update this test
 */
test('can parse array with string values', function () {
    $engine = new BrickEngine();
    $content = '["a", "b", "c"]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)->toBeInstanceOf(ArrayLiteralExpression::class);
});

/**
 * @todo update this test
 */
test('can parse array with key-value pairs', function () {
    $engine = new BrickEngine();
    $content = '[1 => "one", 2 => "two"]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)->toBeInstanceOf(ArrayLiteralExpression::class);
});

/**
 * @todo update this test
 */
test('can parse array with mixed values', function () {
    $engine = new BrickEngine();
    $content = '[1, "two", 3 => "three"]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)->toBeInstanceOf(ArrayLiteralExpression::class);
});
