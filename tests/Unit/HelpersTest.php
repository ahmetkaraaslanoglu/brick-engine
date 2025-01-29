<?php

use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Value;

test('can convert bool to bool object', function () {
    $value = value(true);

    expect($value->type)
        ->toBe(ValueType::Boolean)
        ->and($value->data)
        ->toBeTrue();
});

test('can convert number to number object', function () {
    $value = value(10);

    expect($value->type)
        ->toBe(ValueType::Numeric)
        ->and($value->data)
        ->toBe(10);
});

test('can convert string to string object', function () {
    $value = value('Hello World');

    expect($value->type)
        ->toBe(ValueType::String)
        ->and($value->data)
        ->toBe('Hello World');
});

test('can convert array to array object', function () {
    $value = value([10, 20, 30]);

    expect($value->type)
        ->toBe(ValueType::Array)
        ->and($value->data[0]->data)
        ->toBe(10)
        ->and($value->data[1]->data)
        ->toBe(20)
        ->and($value->data[2]->data)
        ->toBe(30);
});

test('can convert bool object to bool', function () {
    $value = fromValue(new Value(ValueType::Boolean, true));

    expect($value)->toBeTrue();
});

test('can convert number object to number', function () {
    $value = fromValue(new Value(ValueType::Numeric, 10));

    expect($value)->toBe(10);
});

test('can convert string object to string', function () {
    $value = fromValue(new Value(ValueType::String, 'Hello World'));

    expect($value)->toBe('Hello World');
});

test('can convert array object to array', function () {
    $value = fromValue(new Value(ValueType::Array, [
        new Value(ValueType::Numeric, 10),
        new Value(ValueType::Numeric, 20),
        new Value(ValueType::Numeric, 30),
    ]));

    expect($value)->toBe([10, 20, 30]);
});

test('can convert null to null object', function () {
    $value = value(null);

    expect($value->type)
        ->toBe(ValueType::Null);
});

test('can convert null object to null', function () {
    $value = fromValue(new Value(ValueType::Null));

    expect($value)->toBeNull();
});
