<?php

require_once __DIR__ . '/vendor/autoload.php';

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

$contents = file_get_contents(__DIR__ . '/examples/code.txt');

$engine = new BrickEngine();
$engine->context = new Context(functions: [
    'asd' => function (Context $context) use ($engine): Value {
        $arguments = array_map(function ($argument) use ($context) {
            return $context->value($argument)->data;
        }, $context->arguments);
        $value = array_reduce($arguments, fn ($a, $b) => $a + $b, 0);

        return new Value($context, ValueType::Numeric, $value);
    },
    'echo' => function (Context $context) use ($engine): Value {
        foreach ($context->arguments as $argument) {
            print(sprintf("%s\n", $engine->context->value($argument)->data));
        }

        return new Value($context, ValueType::Void);
    },
]);
$engine->context->setVariable('asd', 123);


$result = $engine->run($contents);
dd($result->value->data);
