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
            
            while (i <= n) {
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
        
        return [fib0, fib1, fib2, fib3];
    ';

    $result = $engine->run($code);
    $data = $result->value->data;

    expect(fromValue($data[0]))->toBe(0);
    expect(fromValue($data[1]))->toBe(1);
    expect(fromValue($data[2]))->toBe(1);
    expect(fromValue($data[3]))->toBe(2);
}); // @todo fix problems when calculating fib 4
