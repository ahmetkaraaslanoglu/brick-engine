<?php

require_once __DIR__ . '/vendor/autoload.php';

use IsaEken\BrickEngine\BrickEngine;

$contents = file_get_contents(__DIR__ . '/examples/code.txt');

$engine = new BrickEngine();
$engine->context
    ->setVariable('asd', 123)
    ->setFunction('asd', function (...$arguments) {
        return array_reduce($arguments, fn ($a, $b) => $a + $b, 0);
    })
    ->setFunction('echo', function (...$arguments) {
        foreach ($arguments as $argument) {
            print(sprintf("%s\n", $argument));
        }
    });

$result = $engine->run($contents);
dd($result->value->data);
