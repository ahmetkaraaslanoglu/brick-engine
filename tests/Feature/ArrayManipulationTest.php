<?php

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Runtime\Context;

test('can manipulate arrays', function () {
    $engine = new BrickEngine();

    $code = '
        numbers = [1, 2, 3];
        numbers[3] = 4;
        
        numbers[0] = 10;
        
        sum = 0;
        foreach (numbers as num) {
            sum = sum + num;
        }
        
        filtered = [];
        i = 0;
        foreach (numbers as num) {
            if (num > 2) {
                filtered[i] = num;
                i = i + 1;
            }
        }
        
        result = [
            "original" => numbers,
            "sum" => sum,
            "filtered" => filtered
        ];
        
        return result;
    ';

    $result = $engine->run($code);
    $data = $result->value->data;

    // Original array: [10, 2, 3, 4]
    expect($data["original"]->data[0]->data)->toBe(10);
    expect($data["original"]->data[1]->data)->toBe(2);
    expect($data["original"]->data[2]->data)->toBe(3);
    expect($data["original"]->data[3]->data)->toBe(4);

    // Sum: 19 (10 + 2 + 3 + 4)
    expect($data["sum"]->data)->toBe(19);

    // Filtered array (> 2): [10, 3, 4]
    expect($data["filtered"]->data[0]->data)->toBe(10);
    expect($data["filtered"]->data[1]->data)->toBe(3);
    expect($data["filtered"]->data[2]->data)->toBe(4);
})->skip('Not implemented yet');
