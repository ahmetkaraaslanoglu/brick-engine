<?php

namespace IsaEken\BrickEngine;

use IsaEken\BrickEngine\Contracts\ExpressionInterface;
use IsaEken\BrickEngine\Contracts\StatementInterface;
use IsaEken\BrickEngine\DataObjects\Token;
use IsaEken\BrickEngine\Exceptions\UnexpectedTokenException;
use IsaEken\BrickEngine\Expressions\ArrayAccessExpression;
use IsaEken\BrickEngine\Expressions\ArrayElementExpression;
use IsaEken\BrickEngine\Expressions\ArrayLiteralExpression;
use IsaEken\BrickEngine\Expressions\BinaryExpression;
use IsaEken\BrickEngine\Expressions\ClosureExpression;
use IsaEken\BrickEngine\Expressions\FunctionCallExpression;
use IsaEken\BrickEngine\Expressions\IdentifierExpression;
use IsaEken\BrickEngine\Expressions\LiteralExpression;
use IsaEken\BrickEngine\Expressions\ObjectLiteralExpression;
use IsaEken\BrickEngine\Expressions\ParamDefinitionExpression;
use IsaEken\BrickEngine\Expressions\UnaryExpression;
use IsaEken\BrickEngine\Statements\AssignmentStatement;
use IsaEken\BrickEngine\Statements\BlockStatement;
use IsaEken\BrickEngine\Statements\ExpressionStatement;
use IsaEken\BrickEngine\Statements\ForeachStatement;
use IsaEken\BrickEngine\Statements\ForStatement;
use IsaEken\BrickEngine\Statements\FunctionDeclareStatement;
use IsaEken\BrickEngine\Statements\IfStatement;
use IsaEken\BrickEngine\Statements\ProgramStatement;
use IsaEken\BrickEngine\Statements\ReturnStatement;
use IsaEken\BrickEngine\Statements\WhileStatement;

class Parser
{
    protected array $precedences = [
        'MULTIPLY'        => [70, 'left'], // *
        'DIVIDE'          => [70, 'left'], // /
        'MODULUS'         => [70, 'left'], // %
        'PLUS'            => [60, 'left'], // +
        'MINUS'           => [60, 'left'], // -
        'LESS_THAN'       => [50, 'left'], // <
        'GREATER_THAN'    => [50, 'left'],
        'LESS_THAN_OR_EQUAL' => [50, 'left'],
        'GREATER_THAN_OR_EQUAL' => [50, 'left'],
        'LESS_EQUAL'      => [50, 'left'],
        'GREATER_EQUAL'   => [50, 'left'],
        'EQUALS'     => [40, 'left'], // ==
        'NOT_EQUAL'       => [40, 'left'], // !=
        'AND'         => [30, 'left'], // &&
        'OR'           => [20, 'left'], // ||

        'PLUS_EQUAL'      => [10, 'right'],
        'MINUS_EQUAL'     => [10, 'right'],
        'MULTIPLY_EQUAL'  => [10, 'right'],
        'DIVIDE_EQUAL'    => [10, 'right'],
        'MODULUS_EQUAL'   => [10, 'right'],
        'POWER_EQUAL'     => [10, 'right'],
        'AND_EQUAL'       => [10, 'right'],
        'OR_EQUAL'        => [10, 'right'],
        'LEFT_SHIFT'      => [10, 'right'],
        'RIGHT_SHIFT'     => [10, 'right'],
        'UNSIGNED_RIGHT_SHIFT' => [10, 'right'],
        'LEFT_SHIFT_EQUAL' => [10, 'right'],
        'RIGHT_SHIFT_EQUAL' => [10, 'right'],

        'INCREMENT'       => [10, 'right'],
        'DECREMENT'       => [10, 'right'],
    ];

    public array $statements = [];

    public Token|null $token;

    public function __construct(public array $tokens, public string $content)
    {
        $this->token = array_shift($this->tokens);
    }

    public function isEof(): bool
    {
        return $this->token?->token === 'EOF' || $this->token === null;
    }

    public function peek(int $offset = 0): Token|null
    {
        return $this->tokens[$offset] ?? null;
    }

    public function eat(string $expectedToken): Token|null
    {
        if ($this->token && $this->token->token === $expectedToken) {
            $current = $this->token;
            $this->token = array_shift($this->tokens);
            return $current;
        }

        throw new UnexpectedTokenException(
            content: $this->content,
            start: $this->token?->start ?? 0,
            end: $this->token?->end ?? 0,
            expected: $expectedToken,
            got: $this->token?->token ?? '',
        );
    }

    public function parseExpression(): ExpressionInterface
    {
        return $this->parseExpressionPrecedence(0);
    }

    public function parseClosureExpression(): ExpressionInterface
    {
        $this->eat('FUNCTION');
        if ($this->token->token === 'IDENTIFIER') {
            $callee = $this->token->value;
            $this->eat('IDENTIFIER');
        } else {
            $callee = null;
        }
        $this->eat('LEFT_PARENTHESIS');
        $arguments = $this->parseFunctionArguments();
        $this->eat('RIGHT_PARENTHESIS');
        $body = $this->parseBlock();

        return new ClosureExpression($callee, $arguments, $body);
    }

    public function parseExpressionPrecedence(int $minPrecedence = 0): ExpressionInterface
    {
        $left = $this->parseFactor();

        while (! $this->isEof()) {
            $token = $this->token->token;
            $info = $this->precedences[$token] ?? null;
            if ($info === null) {
                break;
            }

            [$operatorPrecedence, $associativity] = $info;
            if ($operatorPrecedence < $minPrecedence) {
                break;
            }

            $operatorToken = $this->token;
            $this->eat($token);

            $nextMinPrecedence = $associativity === 'left' ? $operatorPrecedence + 1 : $operatorPrecedence;
            if (in_array($token, ['INCREMENT', 'DECREMENT'])) {
                $left = new UnaryExpression(
                    operator: $operatorToken->value,
                    left: $left,
                );
            } else {
                $right = $this->parseExpressionPrecedence($nextMinPrecedence);

                $left = new BinaryExpression(
                    operator: $operatorToken->value,
                    left: $left,
                    right: $right,
                );
            }
        }

        return $left;
    }

    public function parseFactor(): ExpressionInterface
    {
        if ($this->token->token === 'FUNCTION') {
            return $this->parseClosureExpression();
        }

        if ($this->token->value === 'LEFT_PARENTHESIS') {
            $this->eat('LEFT_PARENTHESIS');
            $expression = $this->parseExpression();
            $this->eat('RIGHT_PARENTHESIS');
            return $expression;
        }

        if ($this->token->token === 'LEFT_BRACKET') {
            $this->eat('LEFT_BRACKET');
            return $this->parseArrayLiteral();
        }

        if ($this->token->token === 'LEFT_BRACE') {
            $this->eat('LEFT_BRACE');
            return $this->parseObjectLiteral();
        }

        if ($this->token->token === 'IDENTIFIER') {
            if ($this->peek()?->token === 'LEFT_PARENTHESIS') {
                return $this->parseFunctionCall();
            } else {
                return $this->parseIdentifierOrArrayAccess();
            }
        }

        return $this->parsePrimary();
    }

    public function parseArrayLiteral(): ExpressionInterface
    {
        $elements = [];

        if ($this->token->token === 'RIGHT_BRACKET') {
            $this->eat('RIGHT_BRACKET');
            return new ArrayLiteralExpression($elements);
        }

        while (!$this->isEof()) {
            if ($this->token->token === 'ELLIPSIS') {
                $this->eat('ELLIPSIS');
                $expression = $this->parseExpression();
                $elements[] = new ArrayElementExpression(
                    spread: true,
                    key: null,
                    value: $expression,
                );
            } else {
                $keyOrValue = $this->parseExpression();

                if ($this->token->token === 'ARROW') {
                    $this->eat('ARROW');
                    $expression = $this->parseExpression();
                    $elements[] = new ArrayElementExpression(
                        spread: false,
                        key: $keyOrValue,
                        value: $expression,
                    );
                } else {
                    $elements[] = new ArrayElementExpression(
                        spread: false,
                        key: null,
                        value: $keyOrValue,
                    );
                }
            }

            if ($this->token->token === 'COMMA') {
                $this->eat('COMMA');

                if ($this->token->token === 'RIGHT_BRACKET') {
                    $this->eat('RIGHT_BRACKET');
                    break;
                }
            } else if ($this->token->token == 'RIGHT_BRACKET') {
                $this->eat('RIGHT_BRACKET');
                break;
            } else {
                throw new UnexpectedTokenException(
                    content: $this->content,
                    start: $this->token->start,
                    end: $this->token->end,
                    expected: 'COMMA or RIGHT_BRACKET',
                    got: $this->token->token,
                );
            }
        }

        return new ArrayLiteralExpression($elements);
    }

    public function parseObjectLiteral(): ExpressionInterface
    {
        $elements = [];

        if ($this->token->token === 'RIGHT_BRACE') {
            $this->eat('RIGHT_BRACE');
            return new ObjectLiteralExpression($elements);
        }

        while (!$this->isEof()) {
            if ($this->token->token === 'STRING' || $this->token->token === 'IDENTIFIER') {
                $key = $this->token->value;
                $this->eat($this->token->token);
                $this->eat('COLON');
                $value = $this->parseExpression();
                $elements[$key] = $value;
            } else if ($this->token->token === 'COMMA') {
                $this->eat('COMMA');

                if ($this->token->token === 'RIGHT_BRACE') {
                    $this->eat('RIGHT_BRACE');
                    break;
                }
            } else if ($this->token->token == 'RIGHT_BRACE') {
                $this->eat('RIGHT_BRACE');
                break;
            }
        }

        return new ObjectLiteralExpression($elements);
    }

    public function parseIdentifierOrArrayAccess(): ExpressionInterface
    {
        $identifierToken = $this->token;
        $this->eat('IDENTIFIER');

        $node = new IdentifierExpression($identifierToken->value);

        while (!$this->isEof() && $this->token->token === 'LEFT_BRACKET') {
            $this->eat('LEFT_BRACKET');

            if ($this->token->token === 'RIGHT_BRACKET') {
                $this->eat('RIGHT_BRACKET');

                $node = new ArrayAccessExpression(
                    identifier: $node,
                    index: null,
                );
            } else {
                $indexExpression = $this->parseExpression();
                $this->eat('RIGHT_BRACKET');

                $node = new ArrayAccessExpression(
                    identifier: $node,
                    index: $indexExpression,
                );
            }
        }

        return $node;
    }

    public function parsePrimary(): ExpressionInterface
    {
        $current = $this->token;
        $tokens = ['NUMBER', 'STRING', 'TRUE', 'FALSE', 'NULL'];

        if (in_array($current->token, $tokens)) {
            $this->eat($current->token);

            return new LiteralExpression($current->token, $current->value);
        }

        throw new UnexpectedTokenException(
            content: $this->content,
            start: $current->start,
            end: $current->end,
            expected: implode(', ', $tokens),
            got: $current->token,
        );
    }

    public function parseArguments(): array
    {
        $args = [];
        $args[] = $this->parseExpression();
        while (! $this->isEof() && $this->token->token === 'COMMA') {
            $this->eat('COMMA');
            $args[] = $this->parseExpression();
        }

        return $args;
    }

    public function parseFunctionCall(): ExpressionInterface
    {
        $fnNameToken = $this->token;
        $this->eat('IDENTIFIER');
        $this->eat('LEFT_PARENTHESIS');
        $args = [];
        if ($this->token->token !== 'RIGHT_PARENTHESIS') {
            $args = $this->parseArguments();
        }
        $this->eat('RIGHT_PARENTHESIS');

        return new FunctionCallExpression($fnNameToken->value, $args);
    }

    public function parseAssignment(): StatementInterface
    {
        $left = $this->parseIdentifierOrArrayAccess();
        $this->eat('EQUAL');
        $right = $this->parseExpression();

        return new AssignmentStatement($left, $right);
    }

    public function parseBlock(): StatementInterface
    {
        $this->eat('LEFT_BRACE');
        $statements = [];
        while (! $this->isEof() && $this->token->token !== 'RIGHT_BRACE') {
            $statements[] = $this->parseStatement();
        }
        $this->eat('RIGHT_BRACE');

        return new BlockStatement($statements);
    }

    public function parseExpressionStatement(): StatementInterface
    {
        if ($this->token->token === 'IDENTIFIER') {
            $next = $this->peek();
            if ($next && $next->token === 'EQUAL') {
                $statement = $this->parseAssignment();
                $this->eat('SEMICOLON');
                return $statement;
            }
        }

        $expression = $this->parseExpression();
        if (! in_array($expression::class, [ClosureExpression::class])) {
            $this->eat('SEMICOLON');
        }

        return new ExpressionStatement($expression);
    }

    public function parseIfStatement(): StatementInterface
    {
        $this->eat('IF');
        $this->eat('LEFT_PARENTHESIS');
        $condition = $this->parseExpression();
        $this->eat('RIGHT_PARENTHESIS');
        $thenStatement = $this->parseStatement();

        $elseStatement = null;
        if ($this->token && $this->token->token === 'ELSE') {
            $this->eat('ELSE');
            $elseStatement = $this->parseStatement();
        }

        return new IfStatement($condition, $thenStatement, $elseStatement);
    }

    public function parseWhileStatement(): StatementInterface
    {
        $this->eat('WHILE');
        $this->eat('LEFT_PARENTHESIS');
        $condition = $this->parseExpression();
        $this->eat('RIGHT_PARENTHESIS');
        $body = $this->parseStatement();

        return new WhileStatement($condition, $body);
    }

    public function parseForStatement(): StatementInterface
    {
        $this->eat('FOR');
        $this->eat('LEFT_PARENTHESIS');

        $init = null;
        if ($this->token->token !== 'SEMICOLON') {
            $init = $this->parseExpressionStatement();
        } else {
            $this->eat('SEMICOLON');
        }

        $condition = null;
        if ($this->token->token !== 'SEMICOLON') {
            $condition = $this->parseExpression();
        }
        $this->eat('SEMICOLON');

        $update = null;
        if ($this->token->token !== 'RIGHT_PARENTHESIS') {
            $update = $this->parseExpression();
        }

        $this->eat('RIGHT_PARENTHESIS');
        $body = $this->parseStatement();

        return new ForStatement($init, $condition, $update, $body);
    }

    public function parseForeachStatement(): StatementInterface
    {
        $this->eat('FOREACH');
        $this->eat('LEFT_PARENTHESIS');
        $left = $this->parseExpression();

        if ($this->token->token === 'IN') {
            $this->eat('IN');
            $right = $this->parseExpression();
        } else {
            $this->eat('AS');
            $key = new IdentifierExpression($this->token->value);
            $this->eat('IDENTIFIER');

            if ($this->token->token === 'ARROW') {
                $this->eat('ARROW');
                $value = new IdentifierExpression($this->token->value);
                $this->eat('IDENTIFIER');
                $right = new ArrayElementExpression(
                    spread: false,
                    key: $key,
                    value: $value,
                );
            } else {
                $right = $key;
                $key = null;
            }
        }

        $this->eat('RIGHT_PARENTHESIS');
        $body = $this->parseStatement();

        return new ForeachStatement(
            $left,
            $right,
            $body,
        );
    }

    public function parseFunctionArguments(): array
    {
        if ($this->token->token === 'RIGHT_PARENTHESIS') {
            return [];
        }

        $arguments = [];
        while (! $this->isEof()) {
            $identifier = $this->token;
            $this->eat('IDENTIFIER');
            $argument = new ParamDefinitionExpression($identifier->value, null);

            if ($this->token->token === 'EQUAL') {
                $this->eat('EQUAL');
                $argument->set('default_value', $this->parseExpression());
            }

            $arguments[] = $argument;

            if ($this->token->token === 'RIGHT_PARENTHESIS') {
                break;
            }

            $this->eat('COMMA');
        }

        return $arguments;
    }

    public function parseFunctionDeclareStatement(): StatementInterface
    {
        $this->eat('FUNCTION');
        $callee = $this->token->value;
        $this->eat('IDENTIFIER');
        $this->eat('LEFT_PARENTHESIS');
        $arguments = $this->parseFunctionArguments();
        $this->eat('RIGHT_PARENTHESIS');
        $body = $this->parseStatement();

        return new FunctionDeclareStatement($callee, $arguments, $body);
    }

    public function parseReturnStatement(): StatementInterface
    {
        $this->eat('RETURN');
        $value = null;
        if ($this->token && $this->token->token !== 'SEMICOLON') {
            $value = $this->parseExpression();
        }

        $this->eat('SEMICOLON');

        return new ReturnStatement($value);
    }

    public function parseStatement(): StatementInterface
    {
        if ($this->token->token === 'COMMENT') {
            $this->eat('COMMENT');
            return $this->parseStatement();
        }

        if ($this->token->token === 'LEFT_BRACE') {
            return $this->parseBlock();
        }

        if ($this->token->token === 'IF') {
            return $this->parseIfStatement();
        }

        if ($this->token->token === 'WHILE') {
            return $this->parseWhileStatement();
        }

        if ($this->token->token === 'FOR') {
            return $this->parseForStatement();
        }

        if ($this->token->token === 'FOREACH') {
            return $this->parseForeachStatement();
        }

        // @todo deprecate this
//        if ($this->token->token === 'FUNCTION') {
//            return $this->parseFunctionDeclareStatement();
//        }

        if ($this->token->token === 'RETURN') {
            return $this->parseReturnStatement();
        }

        return $this->parseExpressionStatement();
    }

    public function parseProgram(): StatementInterface
    {
        $statements = [];

        while (! $this->isEof()) {
            $statements[] = $this->parseStatement();
        }

        return new ProgramStatement($statements);
    }
}
