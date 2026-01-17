<?php

namespace SprintF\Bundle\Datetime\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\TypedExpression;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

/**
 * DateRangeContainsDate := "daterange_contains_date" "(" ArithmeticPrimary "," ArithmeticPrimary ")".
 */
class DateRangeContainsDate extends FunctionNode implements TypedExpression
{
    public $rangeExpression;
    public $elementExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s::daterange @> %s::date',
            $this->rangeExpression->dispatch($sqlWalker),
            $this->elementExpression->dispatch($sqlWalker)
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->rangeExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->elementExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getReturnType(): Type
    {
        return Type::getType(Types::BOOLEAN);
    }
}
