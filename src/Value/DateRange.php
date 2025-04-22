<?php

namespace SprintF\Bundle\Datetime\Value;

use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class DateRange implements \Stringable
{
    private const string MASK = '(\[|\()"?(\d{4}-\d{2}-\d{2})?"?,"?(\d{4}-\d{2}-\d{2})?"?(\]|\))';

    public function __construct(
        /** Дата начала диапазона, всегда включительно */
        #[Context(
            normalizationContext: [DateTimeNormalizer::FORMAT_KEY => Date::ISO8601_DATE_ONLY],
            denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!'.Date::ISO8601_DATE_ONLY],
        )]
        private ?Date $beginDate,

        /** Дата конца диапазона, всегда исключительно */
        #[Context(
            normalizationContext: [DateTimeNormalizer::FORMAT_KEY => Date::ISO8601_DATE_ONLY],
            denormalizationContext: [DateTimeNormalizer::FORMAT_KEY => '!'.Date::ISO8601_DATE_ONLY],
        )]
        private ?Date $endDate,
    ) {
        if (null !== $beginDate && null !== $endDate && $beginDate > $endDate) {
            throw new \InvalidArgumentException();
        }
    }

    public static function fromString(string $string): self
    {
        if (!preg_match('~^'.self::MASK.'$~', $string, $matches)) {
            throw new \InvalidArgumentException();
        }

        $lowerLimit = $matches[1];
        $upperLimit = $matches[4];

        $beginDate = empty($matches[2]) ? null : Date::createFromFormat('!'.Date::ISO8601_DATE_ONLY, $matches[2]);
        $endDate = empty($matches[3]) ? null : Date::createFromFormat('!'.Date::ISO8601_DATE_ONLY, $matches[3]);

        if (null !== $beginDate && '(' === $lowerLimit) {
            $beginDate = $beginDate->modify('+1 day');
        }

        if (null !== $endDate && ']' === $upperLimit) {
            $endDate = $endDate->modify('+1 day');
        }

        return new self($beginDate, $endDate);
    }

    public function getBeginDate(): ?Date
    {
        return $this->beginDate;
    }

    public function setBeginDate(?Date $date): self
    {
        $this->beginDate = $date;

        return $this;
    }

    public function getEndDate(): ?Date
    {
        return $this->endDate;
    }

    public function setEndDate(?Date $date): self
    {
        $this->endDate = $date;

        return $this;
    }

    public function containsDate(Date $date): bool
    {
        if (null === $this->getBeginDate() && null === $this->getEndDate()) {
            return true;
        }

        if (null === $this->getBeginDate()) {
            return $date < $this->getEndDate();
        }

        if (null === $this->getEndDate()) {
            return $this->getBeginDate() <= $date;
        }

        return $this->getBeginDate() <= $date && $date < $this->getEndDate();
    }

    /**
     * Проверка на совпадение двух периодов.
     */
    public function equals(self $that): bool
    {
        return $this->getBeginDate() == $that->getBeginDate() && $this->getEndDate() == $that->getEndDate();
    }

    /**
     * Проверка на то, что этот период содержит в себе другой.
     * Проверка нестрогая - совпадающие диапазоны тоже будут содержать в себе друг друга.
     */
    public function contains(self $that): bool
    {
        return
            (null === $this->getBeginDate() || $this->getBeginDate() <= $that->getBeginDate())
            && (null === $this->getEndDate() || (null !== $that->getEndDate() && $that->getEndDate() <= $this->getEndDate()));
    }

    /**
     * Проверка на пересечение двух периодов.
     */
    public function intersects(self $that): bool
    {
        // Один из диапазонов бесконечный - они точно пересекаются
        if (
            (null === $this->getBeginDate() && null === $this->getEndDate())
            || (null === $that->getBeginDate() && null === $that->getEndDate())
        ) {
            return true;
        }

        // Оба диапазона имеют бесконечную нижнюю границу - они точно пересекаются
        if (null === $this->getBeginDate() && null === $that->getBeginDate()) {
            return true;
        }
        // Оба диапазона имеют бесконечную верхнюю границу - они точно пересекаются
        if (null === $this->getEndDate() && null === $that->getEndDate()) {
            return true;
        }

        // Один из диапазонов имеет бесконечную нижнюю границу.
        // Они пересекаются, если другой начинается не позже конца первого.
        if (null === $this->getBeginDate()) {
            return $that->getBeginDate() < $this->getEndDate();
        }
        if (null === $that->getBeginDate()) {
            return $this->getBeginDate() < $that->getEndDate();
        }

        // Один из диапазонов имеет бесконечную верхнюю границу.
        // Они пересекаются, если другой кончается не раньше начала первого.
        if (null === $this->getEndDate()) {
            return $this->getBeginDate() < $that->getEndDate();
        }
        if (null === $that->getEndDate()) {
            return $that->getBeginDate() < $this->getEndDate();
        }

        // Ну и в ситуации, когда оба диапазона конечны, они не пересекаются,
        // если первый закончится раньше, чем начнется второй
        if (
            ($this->getEndDate() <= $that->getBeginDate())
            || ($that->getEndDate() <= $this->getBeginDate())
        ) {
            return false;
        }

        return true;
    }

    public function toString(): string
    {
        $string = null === $this->getBeginDate() ? '(' : '[';

        $string .= (null === $this->getBeginDate()) ? ',' : $this->getBeginDate()->format(Date::ISO8601_DATE_ONLY).',';
        $string .= (null === $this->getEndDate()) ? ')' : $this->getEndDate()->format(Date::ISO8601_DATE_ONLY).')';

        return $string;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
