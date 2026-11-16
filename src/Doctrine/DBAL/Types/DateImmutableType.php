<?php

namespace SprintF\Bundle\Datetime\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use SprintF\ValueObjects\Type\DateImmutable;

class DateImmutableType extends \Doctrine\DBAL\Types\DateImmutableType
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

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DateImmutable
    {
        if (null === $value || $value instanceof DateImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return DateImmutable::createFromInterface($value);
        }

        $date = DateImmutable::createFromFormat('!'.$platform->getDateFormatString(), $value);
        if (false !== $date) {
            return $date;
        }

        throw InvalidFormat::new($value, static::class, $platform->getDateFormatString());
    }
}
