<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Runtime\Context;

test('can calculate fibonacci sequence', function () {
    $engine = new BrickEngine();

    $code = '
        function fibonacci(n) {
            if (n < 2) {
                return n;
            }
            
            a = 0;
            b = 1;
            i = 2;
            
            while (i < n) {
                temp = b;
                b = a + b;
                a = temp;
                i = i + 1;
            }
            
            return b;
        }
        
        fib0 = fibonacci(0);
        fib1 = fibonacci(1);
        fib2 = fibonacci(2);
        fib3 = fibonacci(3);
        fib4 = fibonacci(4);
        fib5 = fibonacci(5);
        
        return [fib0, fib1, fib2, fib3, fib4, fib5];
    ';

    $result = $engine->run($code);
    $data = $result->value->data;

    expect($data[0]->data)->toBe(0);
    expect($data[1]->data)->toBe(1);
    expect($data[2]->data)->toBe(1);
    expect($data[3]->data)->toBe(2);
    expect($data[4]->data)->toBe(3);
    expect($data[5]->data)->toBe(5);
})->skip('Not implemented yet');
