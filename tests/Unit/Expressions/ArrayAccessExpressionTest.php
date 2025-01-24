<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Expressions\ArrayAccessExpression;
use IsaEken\BrickEngine\Lexers\Lexer;
use IsaEken\BrickEngine\Parser;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

test('can parse array access with numeric index', function () {
    $engine = new BrickEngine(new Context([
        'arr' => new Value(ValueType::Array, [new Value(ValueType::Numeric, 42)]),
    ]));

    $content = 'arr[0]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseIdentifierOrArrayAccess();

    expect($expression)->toBeInstanceOf(ArrayAccessExpression::class);
    expect($expression->run($engine->context)->data)->toBe(42);
});

test('can parse array access with index', function () {
    $engine = new BrickEngine(new Context([
        'arr' => new Value(ValueType::Array, [
            new Value(ValueType::Numeric, 10),
            new Value(ValueType::Numeric, 20),
            new Value(ValueType::Numeric, 30),
        ]),
    ]));

    $content = 'arr[1]';
    $lexer = new Lexer($engine, $content);
    $tokens = $lexer->run();

    $parser = new Parser($tokens, $content);
    $expression = $parser->parseIdentifierOrArrayAccess();

    expect($expression)->toBeInstanceOf(ArrayAccessExpression::class);
    expect($expression->run($engine->context)->data)->toBe(20);
});
