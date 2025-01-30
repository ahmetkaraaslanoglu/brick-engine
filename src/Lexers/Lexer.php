<?php

namespace IsaEken\BrickEngine\Lexers;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\DataObjects\Token;
use IsaEken\BrickEngine\Exceptions\UnexpectedCharacterException;

class Lexer extends BaseLexer
{
    public const array RESERVED_KEYWORDS = [
        'IF',
        'ELSE',
        'TRUE',
        'FALSE',
        'NULL',
        'WHILE',
        'FUNCTION',
        'RETURN',
        'FOR',
        'FOREACH',
        'IN',
        'AS',
        'BREAK',
    ];

    public const array SYNTAX = [
        '...' => 'ELLIPSIS',
        '=>' => 'ARROW',
        '||' => 'OR',
        '&&' => 'AND',
        '<=' => 'LESS_THAN_OR_EQUAL',
        '>=' => 'GREATER_THAN_OR_EQUAL',
        '!=' => 'NOT_EQUAL',
        '==' => 'EQUALS',
        '++' => 'INCREMENT',
        '--' => 'DECREMENT',
        '/*' => 'COMMENT_START',
        '*/' => 'COMMENT_END',
        '//' => 'COMMENT',
        '+=' => 'PLUS_EQUAL',
        '-=' => 'MINUS_EQUAL',
        '*=' => 'MULTIPLY_EQUAL',
        '/=' => 'DIVIDE_EQUAL',
        '%=' => 'MODULUS_EQUAL',
        '^=' => 'POWER_EQUAL',
        '&=' => 'AND_EQUAL',
        '|=' => 'OR_EQUAL',
        '<<' => 'LEFT_SHIFT',
        '>>' => 'RIGHT_SHIFT',
        '>>>' => 'UNSIGNED_RIGHT_SHIFT',
        '<<=' => 'LEFT_SHIFT_EQUAL',
        '>>=' => 'RIGHT_SHIFT_EQUAL',
        '<' => 'LESS_THAN',
        '>' => 'GREATER_THAN',
        '|' => 'PIPE',
        '&' => 'AMPERSAND',
        '!' => 'EXCLAMATION',
        '=' => 'EQUAL',
        '(' => 'LEFT_PARENTHESIS',
        ')' => 'RIGHT_PARENTHESIS',
        '{' => 'LEFT_BRACE',
        '}' => 'RIGHT_BRACE',
        '[' => 'LEFT_BRACKET',
        ']' => 'RIGHT_BRACKET',
        ';' => 'SEMICOLON',
        ':' => 'COLON',
        ',' => 'COMMA',
        '.' => 'DOT',
        '+' => 'PLUS',
        '-' => 'MINUS',
        '*' => 'ASTERISK',
        '/' => 'SLASH',
        '%' => 'PERCENT',
        '^' => 'CARET',
        '~' => 'TILDE',
        '@' => 'AT',
        '#' => 'HASH',
        '$' => 'DOLLAR',
        '?' => 'QUESTION',
        '\\' => 'BACKSLASH',
        '\'' => 'SINGLE_QUOTE',
        '"' => 'DOUBLE_QUOTE',
    ];

    public function eat(): Token
    {
        $cursor = $this->cursor;
        $char = $this->content[$cursor] ?? null;
        $this->cursor++;

        if ($char === null) {
            return Token::make('EOF', '', $cursor);
        }

        if ($char === '/' && $this->peekAt() === '*') {
            $this->cursor++;
            $this->cursor++;
            $value = $this->readUntil(fn ($char, $cursor) => $char === '*' && $this->content[$cursor + 1] === '/');
            $this->cursor++;
            $this->cursor++;
            return Token::make('COMMENT', $value, $cursor);
        }

        if ($this->isNumeric($char)) {
            $value = $char . $this->readUntil(fn ($char) => ! ($this->isNumeric($char) || $char === '.'));
            return Token::make('NUMBER', $value, $cursor);
        }

        if ($this->isAlpha($char)) {
            $value = $char . $this->readUntil(fn ($char) => ! $this->isAlphaNumeric($char));
            if (in_array(strtoupper($value), self::RESERVED_KEYWORDS)) {
                return Token::make(strtoupper($value), $value, $cursor);
            }

            return Token::make('IDENTIFIER', $value, $cursor);
        }

        if ($char === '\'') {
            $value = $this->readUntil(fn ($char) => '\'' === $this->content[$this->cursor]);
            $this->cursor++;
            return Token::make('STRING', $value, $cursor);
        }

        if ($char === '"') {
            $value = $this->readUntil(function ($char, $cursor) {
                return '"' === $char && '\\' !== $this->content[$cursor - 1];
            });

            $this->cursor++;
            $replaces = [
                '\n' => "\n",
                '\r' => "\r",
                '\t' => "\t",
                '\v' => "\v",
                '\e' => "\e",
                '\f' => "\f",
                '\0' => "\0",
            ];
            $value = str_replace(array_keys($replaces), array_values($replaces), $value);
            return Token::make('STRING', $value, $cursor);
        }

        foreach (self::SYNTAX as $syntax => $name) {
            $chars = str_split($syntax);
            for ($i = 0; $i < count($chars); $i++) {
                if ($chars[$i] != $this->lookAt($cursor + $i)) {
                    continue 2;
                }
            }

            $this->cursor += count($chars) - 1;
            return Token::make($name, $syntax, $cursor);
        }

        throw new UnexpectedCharacterException($this->content, $cursor);
    }

    public function run(): array
    {
        $max = strlen($this->content);
        while ($this->cursor < $max) {
            $this->skipWhitespaces();
            $this->tokens[] = $this->eat();
        }

        return $this->tokens;
    }
}
