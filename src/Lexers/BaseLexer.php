<?php

namespace IsaEken\BrickEngine\Lexers;

use IsaEken\BrickEngine\BrickEngine;

abstract class BaseLexer
{
    public array $tokens = [];

    public int $cursor = 0;

    public function __construct(
        public readonly BrickEngine $engine,
        public readonly string $content,
    )
    {
        // ...
    }

    public function lookAt(int $cursor = 0): string
    {
        return $this->content[$cursor] ?? '';
    }

    public function peekAt(int $offset = 0): string
    {
        return $this->lookAt($this->cursor + $offset);
    }

    public function readRange(int $start, int $end): string
    {
        $content = '';
        for ($i = $start; $i < min(strlen($this->content), $end); $i++) {
            $content .= $this->content[$i];
        }

        return $content;
    }

    public function readUntil(callable $callback): string
    {
        $content = '';
        $max = strlen($this->content);

        while ($this->cursor < $max) {
            $cursor = $this->cursor;
            $char = $this->content[$cursor];
            if ($callback($char, $cursor)) {
                return $content;
            }

            $this->cursor++;
            $content .= $char;
        }

        return $content;
    }

    public function skipWhitespaces(): void
    {
        $max = strlen($this->content);
        while ($this->cursor < $max) {
            if (!preg_match('/\s/', $this->content[$this->cursor])) {
                return;
            }

            $this->cursor++;
        }
    }

    public function isAlpha(string $char): bool
    {
        return preg_match('/[a-zA-Z_]/', $char) === 1;
    }

    public function isNumeric(string $char): bool
    {
        return in_array($char, range('0', '9'));
    }

    public function isAlphaNumeric(string $char): bool
    {
        return $this->isAlpha($char) || $this->isNumeric($char);
    }

    abstract public function run(): array;
}
