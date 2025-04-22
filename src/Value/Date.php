<?php

namespace SprintF\Bundle\Datetime\Value;

class Date extends \DateTime implements \Stringable
{
    public const string ISO8601_DATE_ONLY = 'Y-m-d';

    public const string RUSSIAN_DATE = 'd.m.Y';

    #[\ReturnTypeWillChange]
    public static function createFromFormat($format, $datetime, $timezone = null): self|false
    {
        return parent::createFromFormat($format, $datetime, $timezone);
    }

    #[\ReturnTypeWillChange]
    public static function createFromInterface(\DateTimeInterface $object): self
    {
        return self::createFromFormat('!'.self::ISO8601_DATE_ONLY, $object->format(self::ISO8601_DATE_ONLY));
    }

    #[\ReturnTypeWillChange]
    public function setTime($hour, $minute, $second = 0, $microsecond = 0): self
    {
        throw new \BadMethodCallException();
    }

    public function __toString(): string
    {
        return $this->format(self::ISO8601_DATE_ONLY);
    }
}
