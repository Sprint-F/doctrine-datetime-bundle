<?php

namespace SprintF\Bundle\Datetime\Component\Form\Transformer;

use SprintF\Bundle\Datetime\Value\Date;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Трансформер данных для форм, для представления данных формы в виде объекта класса Date.
 */
class DateTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        return $value;
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        return Date::createFromInterface($value);
    }
}
