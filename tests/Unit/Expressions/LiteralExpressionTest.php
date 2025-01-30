<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\LiteralExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can be compile to php', function () {
    $content = '42';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('42');

    $content = '"Hello World"';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('"Hello World"');

    $content = 'true';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('true');

    $content = 'false';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('false');

    $content = 'null';
    $parser = new Parser(new Lexer(new BrickEngine(), $content)->run(), $content);

    expect($parser->parseFactor()->compile())->toBe('null');
});

test('can parse numeric literal', function () {
    $engine = new BrickEngine();
    $content = '42';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe(42);
});

test('can parse string literal', function () {
    $engine = new BrickEngine();
    $content = '"Hello World"';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe('Hello World');
});

test('can parse boolean literal', function () {
    $engine = new BrickEngine();
    $content = 'true';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeTrue();

    $content = 'false';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(LiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBeFalse();
});
