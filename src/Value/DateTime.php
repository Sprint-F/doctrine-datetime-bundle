<?php

namespace SprintF\Bundle\Datetime\Value;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * @method static DateTime createFromFormat(string $format, string $datetime, ?\DateTimeZone $timezone = null)
 * @method static DateTime createFromImmutable(DateTimeImmutable $object)
 * @method static DateTime createFromInterface(DateTimeInterface $object)
 */
class DateTime extends \DateTime implements \Stringable
{
    public const string RUSSIAN_DATE_TIME = 'd.m.Y H:i:s';

    public static function createFromTimestamp(int|float $timestamp): self
    {
        if (PHP_VERSION_ID >= 80400) {
            return parent::createFromTimestamp($timestamp);
        } else {
            return static::createFromFormat('U', $timestamp);
        }
    }

    public function __toString(): string
    {
        return $this->format('c');
    }
}
