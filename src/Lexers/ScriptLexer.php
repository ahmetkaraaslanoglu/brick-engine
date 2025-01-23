<?php

namespace IsaEken\BrickEngine\Lexers;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\DataObjects\Token;
use IsaEken\BrickEngine\Exceptions\UnexpectedCharacterException;

class ScriptLexer extends BaseLexer
{
    public function skipUntilNextStatement(): void
    {
        $max = strlen($this->content);

        while ($this->cursor < $max) {
            if (
                $this->content[$this->cursor] === '{' &&
                $this->peekAt(1) === '{' &&
                $this->lookAt($this->cursor - 1) !== '\\'
            ) {
                return;
            }

            $this->cursor++;
        }
    }

    public function eat(string $code, int $start, int $end): void
    {
        try {
            $lexer = new Lexer($this->engine, $code);
            $tokens = $lexer->run();
            $tokens = array_map(function (Token $token) use ($start, $end) {
                $token->start += $start;
                $token->end += $start;
                return $token;
            }, $tokens);

            $this->tokens = [
                ...$this->tokens,
                ...$tokens,
            ];
        } catch (UnexpectedCharacterException $exception) {
            throw $exception
                ->setContent($this->content)
                ->setStart($exception->start + $start)
                ->setEnd($exception->end + $end);
        }
    }

    public function run(): array
    {
        $max = strlen($this->content);
        while ($this->cursor < $max) {
            $this->skipUntilNextStatement();

            if (
                $this->cursor < $max &&
                $this->content[$this->cursor] === '{' &&
                $this->peekAt(1) === '{' &&
                $this->lookAt($this->cursor - 1) !== '\\'
            ) {
                $this->cursor += 2;
                $start = $this->cursor;
                $code = $this->readUntil(fn ($char, $cursor) => $char === '}' && $this->lookAt($cursor + 1) === '}');
                $end = $this->cursor;

                $this->eat($code, $start, $end);
            } else {
                break;
            }
        }

        return $this->tokens;
    }
}
