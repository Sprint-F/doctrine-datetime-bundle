<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use SprintF\ValueObjects\Type\Date;

class DateType extends \Doctrine\DBAL\Types\DateType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Date
    {
        if (null === $value || $value instanceof Date) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Date::createFromInterface($value);
        }

        $date = Date::createFromFormat('!'.$platform->getDateFormatString(), $value);
        if (false !== $date) {
            return $date;
        }

        throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateFormatString());
    }
}
