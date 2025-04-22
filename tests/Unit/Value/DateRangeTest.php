<?php

namespace SprintF\Bundle\Datetime\Tests\Unit\Value;

use SprintF\Bundle\Datetime\Tests\Support\UnitTester;
use SprintF\Bundle\Datetime\Value\Date;
use SprintF\Bundle\Datetime\Value\DateRange;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
        /*
         * Тест пока не реализован. Нет возможности корректно запустить нормалайзер вне приложения Symfony.
         *
        $encoders = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        // Бесконечный всюду диапазон
        $dateRange = new DateRange(null, null);
        $this->assertSame(['beginDate' => null, 'endDate' => null], $serializer->normalize($dateRange));

        // Бесконечный вправо диапазон
        $today = new Date('today');
        $Ymd = $today->format('Y-m-d');
        $dateRange = new DateRange($today, null);
        $this->assertSame(['beginDate' => $Ymd, 'endDate' => null], $serializer->normalize($dateRange));
        */
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
