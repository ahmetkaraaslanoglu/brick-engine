<?php

use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

if (! function_exists('value')) {
    function value($value): Value
    {
        return Value::from(new Context(), $value);
    }
}

if (! function_exists('fromValue')) {
    function fromValue(Value $value): mixed
    {
        return Value::real($value);
    }
}
