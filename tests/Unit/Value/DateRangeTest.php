<?php

namespace SprintF\Bundle\Datetime\Tests\Unit\Value;

use SprintF\Bundle\Datetime\Tests\Support\UnitTester;
use SprintF\Bundle\Datetime\Value\Date;
use SprintF\Bundle\Datetime\Value\DateRange;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DateRangeTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testCreateConstruct()
    {
        // Бесконечный всюду диапазон
        $dateRange = new DateRange(null, null);
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());

        // Бесконечный вправо диапазон
        $dateRange = new DateRange(new Date('today'), null);
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('today'), $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());

        // Бесконечный влево диапазон
        $dateRange = new DateRange(null, new Date('today'));
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertEquals(new Date('today'), $dateRange->getEndDate());

        // Диапазон из одной даты
        $dateRange = new DateRange(new Date('today'), new Date('today'));
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('today'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('today'), $dateRange->getEndDate());

        // Диапазон из нескольких дат
        $dateRange = new DateRange(new Date('today'), new Date('first day of next year'));
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('today'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('first day of next year'), $dateRange->getEndDate());

        // Некорректный диапазон
        $this->expectException(\InvalidArgumentException::class);
        $dateRange = new DateRange(new Date('first day of next year'), new Date('today'));
    }

    public function testNormalization()
    {
        $today = new Date('today');
        $tomorrow = new Date('tomorrow');

        $encoders = [new JsonEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new ObjectNormalizer(classMetadataFactory: new ClassMetadataFactory(new AttributeLoader())),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $object = (new class {
            private $id;

            public function setId($id): self
            {
                $this->id = $id;

                return $this;
            }

            public function getId()
            {
                return $this->id;
            }
            private DateRange $period;

            public function setPeriod(DateRange $period): self
            {
                $this->period = $period;

                return $this;
            }

            public function getPeriod(): DateRange
            {
                return $this->period;
            }
        })->setId(42)->setPeriod(new DateRange($today, $tomorrow));

        $json = $serializer->serialize($object, 'json');

        $this->assertSame('{"id":42,"period":{"beginDate":"'.$today->format(Date::ISO8601_DATE_ONLY).'","endDate":"'.$tomorrow->format(Date::ISO8601_DATE_ONLY).'"}}', $json);
    }

    public function testFromStringInfinite()
    {
        $dateRange = DateRange::fromString('[,)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());

        $dateRange = DateRange::fromString('(,)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());

        $dateRange = DateRange::fromString('[,]');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());
    }

    public function testFromStringInfiniteRight()
    {
        $dateRange = DateRange::fromString('[2021-02-03,)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-02-03'), $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());

        $dateRange = DateRange::fromString('(2021-02-03,)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-02-04'), $dateRange->getBeginDate());
        $this->assertSame(null, $dateRange->getEndDate());
    }

    public function testFromStringInfiniteLeft()
    {
        $dateRange = DateRange::fromString('[,2021-02-03)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-03'), $dateRange->getEndDate());

        $dateRange = DateRange::fromString('[,2021-02-03]');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertSame(null, $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-04'), $dateRange->getEndDate());
    }

    public function testFromStringFinite()
    {
        $dateRange = DateRange::fromString('[2021-01-02,2021-02-03)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-01-02'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-03'), $dateRange->getEndDate());

        $dateRange = DateRange::fromString('(2021-01-02,2021-02-03)');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-01-03'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-03'), $dateRange->getEndDate());

        $dateRange = DateRange::fromString('(2021-01-02,2021-02-03]');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-01-03'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-04'), $dateRange->getEndDate());

        $dateRange = DateRange::fromString('[2021-01-02,2021-02-03]');
        $this->assertInstanceOf(DateRange::class, $dateRange);
        $this->assertEquals(new Date('2021-01-02'), $dateRange->getBeginDate());
        $this->assertEquals(new Date('2021-02-04'), $dateRange->getEndDate());

        $this->expectException(\InvalidArgumentException::class);
        $dateRange = DateRange::fromString('[2021-02-03,2021-01-02]');

        $this->expectException(\InvalidArgumentException::class);
        $dateRange = DateRange::fromString('[something,wrong]');
    }

    public function testFromStringInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $dateRange = DateRange::fromString('[something,wrong]');
    }

    public function testEquality()
    {
        $dateRange1 = DateRange::fromString('[,)');
        $dateRange2 = DateRange::fromString('[,)');
        $this->assertTrue($dateRange1 == $dateRange2);
        $this->assertFalse($dateRange1 === $dateRange2);

        $dateRange1 = DateRange::fromString('[2000-01-01,2001-02-03)');
        $dateRange2 = DateRange::fromString('[2000-01-01,2001-02-03)');
        $this->assertTrue($dateRange1 == $dateRange2);
        $this->assertFalse($dateRange1 === $dateRange2);

        $dateRange1 = DateRange::fromString('[2000-01-01,2001-02-03)');
        $dateRange2 = DateRange::fromString('[2010-01-01,2011-02-03)');
        $this->assertFalse($dateRange1 == $dateRange2);
        $this->assertFalse($dateRange1 === $dateRange2);
    }

    public function testContainsDate()
    {
        $today = new Date('today');
        $dateToday = $today->format('Y-m-d');
        $dateInPast = '2000-01-01';
        $dateInFuture = (new Date('first day of next year'))->format('Y-m-d');

        $this->assertTrue(DateRange::fromString('[,)')->containsDate($today));

        $this->assertTrue(DateRange::fromString('['.$dateInPast.',)')->containsDate($today));
        $this->assertTrue(DateRange::fromString('['.$dateToday.',)')->containsDate($today));
        $this->assertFalse(DateRange::fromString('['.$dateInFuture.',)')->containsDate($today));

        $this->assertFalse(DateRange::fromString('[,'.$dateInPast.')')->containsDate($today));
        $this->assertFalse(DateRange::fromString('[,'.$dateToday.')')->containsDate($today));
        $this->assertTrue(DateRange::fromString('[,'.$dateInFuture.')')->containsDate($today));

        $this->assertFalse(DateRange::fromString('['.$dateInPast.','.$dateToday.')')->containsDate($today));
        $this->assertTrue(DateRange::fromString('['.$dateToday.','.$dateInFuture.')')->containsDate($today));
        $this->assertTrue(DateRange::fromString('['.$dateInPast.','.$dateInFuture.')')->containsDate($today));
    }
}
