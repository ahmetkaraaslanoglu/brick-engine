<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Runtime\Context;

test('can perform basic calculations', function () {
    $engine = new BrickEngine();

    $code = '
        a = 10;
        b = 5;
        
        sum = a + b;
        difference = a - b;
        
        c = sum + difference;
        d = c + a + b;
        
        return d;
    ';

    $result = $engine->run($code);
    expect($result->value->data)->toBe(35); // (10 + 5) + (10 - 5) + (10 + 5) = 15 + 5 + 15 = 35
});
