<?php

namespace AppBundle\ORM\Query\AST;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

class Round extends FunctionNode
{
    public $simpleArithmeticExpression;

    public $roundPrecision;


    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf('ROUND(%s, %s)',
            $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression),
            (null == $this->roundPrecision ? 0 : $sqlWalker->walkStringPrimary($this->roundPrecision))
        );
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
        if (Lexer::T_COMMA == $parser->getLexer()->lookahead['type']) {
            $parser->match(Lexer::T_COMMA);
            $this->roundPrecision = $parser->SimpleArithmeticExpression();
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}