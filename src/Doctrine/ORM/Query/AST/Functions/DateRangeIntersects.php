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
 * DateRangeIntersects := "daterange_intersects" "(" ArithmeticPrimary "," ArithmeticPrimary ")".
 */
class DateRangeIntersects extends FunctionNode implements TypedExpression
{
    public $firstRangeExpression;
    public $secondRangeExpression;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return sprintf('%s::daterange && %s::daterange',
            $this->firstRangeExpression->dispatch($sqlWalker),
            $this->secondRangeExpression->dispatch($sqlWalker)
        );
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->firstRangeExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_COMMA);
        $this->secondRangeExpression = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getReturnType(): Type
    {
        return Type::getType(Types::BOOLEAN);
    }
}
