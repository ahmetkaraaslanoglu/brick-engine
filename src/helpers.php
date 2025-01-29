<?php

use IsaEken\BrickEngine\Value;

if (! function_exists('value')) {
    function value($value): Value
    {
        return Value::from($value);
    }

    function fromValue(Value $value): mixed
    {
        return Value::real($value);
    }
}
