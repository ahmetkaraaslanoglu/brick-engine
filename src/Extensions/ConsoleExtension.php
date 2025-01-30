<?php

namespace IsaEken\BrickEngine\Extensions;

use IsaEken\BrickEngine\BrickEngine;
use IsaEken\BrickEngine\Contracts\ExtensionInterface;
use IsaEken\BrickEngine\Enums\ValueType;
use IsaEken\BrickEngine\Runtime\Context;
use IsaEken\BrickEngine\Value;

class ConsoleExtension implements ExtensionInterface
{
    public function __construct(public BrickEngine $engine)
    {
        // ...
    }

    public function register(): void
    {
        $this->engine->context
            ->setFunction('print', function (...$arguments) {
                foreach ($arguments as $argument) {
                    print(sprintf("%s", $argument));
                }
            })
            ->setFunction('println', function (...$arguments) {
                foreach ($arguments as $argument) {
                    print(sprintf("%s\n", $argument));
                }
            });

        $this->engine->context->namespaces['console'] = [
            'print' => function (...$arguments) {
                foreach ($arguments as $argument) {
                    print(sprintf("%s", $argument));
                }
            },
            'println' => function (...$arguments) {
                foreach ($arguments as $argument) {
                    print(sprintf("%s\n", $argument));
                }
            },
            'color' => fn ($color, ...$arguments) => $this->color($color, ...$arguments),
        ];
    }

    private function color($color, ...$arguments)
    {
        echo match ($color) {
            'red' => "\033[31m",
            'green' => "\033[32m",
            'yellow' => "\033[33m",
            'blue' => "\033[34m",
            'magenta' => "\033[35m",
            'cyan' => "\033[36m",
            'white' => "\033[37m",
            default => "\033[0m",
        };
        foreach ($arguments as $argument) {
            print(sprintf("%s", $argument));
        }
        echo "\033[0m";
    }
}
