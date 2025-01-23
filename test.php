<?php

require_once __DIR__ . '/vendor/autoload.php';

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

$contents = file_get_contents(__DIR__ . '/examples/code.txt');

$engine = new BrickEngine();
$engine->context = new Context([
    'asd' => new Value(ValueType::Numeric, 123),
], [
    'asd' => function (Value $a, Value $b) use ($engine): Value {
        $a = $engine->context->value($a);
        $b = $engine->context->value($b);
        return new Value($a->type, $a->data + $b->data);
    },
    'echo' => function (Value $a) use ($engine): Value {
        echo $engine->context->value($a)->data . PHP_EOL;
        return new Value(ValueType::Void);
    },
]);


$result = $engine->run($contents);
dd($result->value->data);
