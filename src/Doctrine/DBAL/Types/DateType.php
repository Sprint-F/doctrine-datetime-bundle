<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use SprintF\Bundle\Datetime\Value\Date;

class DateType extends \Doctrine\DBAL\Types\DateType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return null === $value ? null : (string) $value;
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
