<?php

namespace IsaEken\BrickEngine\Exceptions;

class UnexpectedTokenException extends InvalidSyntaxException
{
    public function __construct(
        public string $content,
        public int $start,
        public int $end,
        public string $expected,
        public string $got,
    )
    {
        parent::__construct($this->update());
    }

    private function update(): string
    {
        $line = 1;
        $column = 1;

        for ($i = 0; $i < $this->start; $i++) {
            if (($this->content[$i] ?? '') === "\n") {
                $line++;
                $column = 1;
            } else {
                $column++;
            }
        }

        $char = $this->content[$this->start] ?? '';
        $code = explode("\n", $this->content)[$line - 1] ?? null;
        $message = "Unexpected character \"{$char}\" at line {$line}, column {$column}.";
        if ($code && $line && $column) {
            $message .= "\n\n" . $code . "\n" . str_repeat(' ', $column - 1) . "^\n";
        }

        return $this->message = $message;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        $this->update();
        return $this;
    }

    public function setStart(int $start): self
    {
        $this->start = $start;
        $this->update();
        return $this;
    }

    public function setEnd(int $end): self
    {
        $this->end = $end;
        $this->update();
        return $this;
    }
}
