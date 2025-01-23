<?php

namespace IsaEken\BrickEngine\Enums;

enum ValueType: string
{
    case Null = 'NULL';
    case Numeric = 'NUMERIC';
    case String = 'STRING';
    case Boolean = 'BOOLEAN';
    case Array = 'ARRAY';
    case ArrayElement = 'ARRAY_ELEMENT';
    case Function = 'FUNCTION';
    case Void = 'VOID';
    case Identifier = 'IDENTIFIER';
}
