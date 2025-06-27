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
 * DateRangeLower := "daterange_lower" "(" ArithmeticPrimary ")".
 */
class DateRangeLower extends FunctionNode implements TypedExpression
{
    public $rangeExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('lower(%s::daterange)',
            $this->rangeExpression->dispatch($sqlWalker),
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->rangeExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getReturnType(): Type
    {
        return Type::getType(Types::DATETIME_MUTABLE);
    }
}
