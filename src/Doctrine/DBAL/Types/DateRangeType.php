<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use SprintF\Bundle\Datetime\Value\DateRange;

class DateRangeType extends Type
{
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        return null === $value ? null : (string) $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateRange
    {
        return null === $value ? null : DateRange::fromString($value);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'daterange';
    }

    public function getName(): string
    {
        return 'daterange';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return false;
    }
}
