<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use SprintF\ValueObjects\Type\DateTimeImmutable;

class DateTimeImmutableType extends \Doctrine\DBAL\Types\DateTimeImmutableType
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
    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if (null === $value || $value instanceof DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $value);
        if (false !== $dateTime) {
            return $dateTime;
        }

        throw InvalidFormat::new($value, static::class, $platform->getDateTimeFormatString());
    }
}
