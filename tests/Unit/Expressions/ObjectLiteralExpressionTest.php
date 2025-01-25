<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Expressions\ObjectLiteralExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;

test('can parse empty object', function () {
    $engine = new BrickEngine();
    $content = '{}';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();

    expect($expression)
        ->toBeInstanceOf(ObjectLiteralExpression::class)
        ->and($expression->run($engine->context)->data)
        ->toBe([]);
});

test('can parse object with values', function () {
    $engine = new BrickEngine();
    $content = <<<CODE
{
    "a": 1,
    "b": 'foo',
    "c": true,
    d: null,
    'e': [1, 2, 3],
    "f": {
        "a": 1,
        "b": 2,
        "c": 3,
    },
}
CODE;
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseFactor();
    $data = $expression->run($engine->context)->data;

    expect($expression)
        ->toBeInstanceOf(ObjectLiteralExpression::class)
        ->and($data['a']->data)
        ->toBe(1)
        ->and($data['b']->data)
        ->toBe('foo')
        ->and($data['c']->data)
        ->toBeTrue()
        ->and($data['d']->data)
        ->toBeNull()
        ->and($data['e']->data[0]->data)
        ->toBe(1)
        ->and($data['f']->data['a']->data)
        ->toBe(1);
});
