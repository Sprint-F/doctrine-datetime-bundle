<?php

namespace SprintF\Bundle\Datetime\Component\Serializer\Normalizer;

use SprintF\Bundle\Datetime\Value\Date;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Специальный нормалайзер для типа Date.
 * Нужен, поскольку стандартная денормализация упорно суёт текущее время в восстановленное значение.
 */
class DateNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_string($data) || '' === trim($data)) {
            throw NotNormalizableValueException::createForUnexpectedDataType('The data is either not an string, an empty string, or null; you should pass a string that can be parsed with the passed format or a valid Date string.', $data, ['string'], $context['deserialization_path'] ?? null, true);
        }

        try {
            $result = Date::createFromFormat('!'.Date::ISO8601_DATE_ONLY, $data);
            if (false === $result) {
                throw NotNormalizableValueException::createForUnexpectedDataType('The data is not a valid date string, or null; you should pass a string that can be parsed with the passed format or a valid Date string.', $data, ['string'], $context['deserialization_path'] ?? null, true);
            }

            return $result;
        } catch (NotNormalizableValueException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw NotNormalizableValueException::createForUnexpectedDataType($e->getMessage(), $data, ['string'], $context['deserialization_path'] ?? null, false, $e->getCode(), $e);
        }
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return Date::class === $type;
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if (!$data instanceof Date) {
            throw new InvalidArgumentException();
        }

        return $data->format(Date::ISO8601_DATE_ONLY);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Date;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Date::class => true,
        ];
    }
}
