<?php

namespace SprintF\Bundle\Datetime\Tests\Unit\Component\Serializer\Normalizer;

use SprintF\Bundle\Datetime\Component\Serializer\Normalizer\DateNormalizer;
use SprintF\Bundle\Datetime\Tests\Support\UnitTester;
use SprintF\Bundle\Datetime\Value\Date;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class DateNormalizerTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testNormalizeInvalidArgument()
    {
        $normalizer = new DateNormalizer();

        $this->expectException(InvalidArgumentException::class);
        $normalized = $normalizer->normalize(new \stdClass());
    }

    public function testNormalize()
    {
        $normalizer = new DateNormalizer();

        $normalized = $normalizer->normalize(new Date('today'));
        $this->assertIsString($normalized);
        $this->assertSame((new \DateTime('today'))->format('Y-m-d'), $normalized);
    }

    public function testDenormalizeNotNormalizableValueNotString()
    {
        $normalizer = new DateNormalizer();

        $this->expectException(NotNormalizableValueException::class);
        $denormalized = $normalizer->denormalize(42, Date::class);
    }

    public function testDenormalizeNotNormalizableValueEmptyString()
    {
        $normalizer = new DateNormalizer();

        $this->expectException(NotNormalizableValueException::class);
        $denormalized = $normalizer->denormalize('', Date::class);
    }

    public function testDenormalizeNotNormalizableValueInvalidString()
    {
        $normalizer = new DateNormalizer();

        $this->expectException(NotNormalizableValueException::class);
        $denormalized = $normalizer->denormalize('foobar', Date::class);
    }

    public function testDenormalize()
    {
        $normalizer = new DateNormalizer();

        $denormalized = $normalizer->denormalize('2001-02-03', Date::class);
        $this->assertEquals(new Date('2001-02-03'), $denormalized);
    }
}
