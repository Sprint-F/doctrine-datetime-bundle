<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use SprintF\Bundle\Datetime\Value\DateTime;

class DateTimeType extends \Doctrine\DBAL\Types\DateTimeType
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

    #[\ReturnTypeWillChange]
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTime
    {
        if (null === $value || $value instanceof DateTime) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return DateTime::createFromInterface($value);
        }

        $dateTime = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
        if (false !== $dateTime) {
            return $dateTime;
        }

        throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
    }
}
