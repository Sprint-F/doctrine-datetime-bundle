<?php

namespace SprintF\Bundle\Datetime\Tests\Unit\Value;

use SprintF\Bundle\Datetime\Tests\Support\UnitTester;
use SprintF\Bundle\Datetime\Value\DateTime;

class DateTimeTest extends \Codeception\Test\Unit
{
    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testCreateConstruct()
    {
        $datetime = new DateTime();
        $this->assertInstanceOf(DateTime::class, $datetime);
        $today = new \DateTime('now');
        $this->assertSame($today->format(\DateTime::RFC3339_EXTENDED), $datetime->format(DateTime::RFC3339_EXTENDED));

        $datetime = new DateTime('now');
        $this->assertInstanceOf(DateTime::class, $datetime);
        $today = new \DateTime('now');
        $this->assertSame($today->format(\DateTime::RFC3339_EXTENDED), $datetime->format(DateTime::RFC3339_EXTENDED));

        $this->expectException(\DateMalformedStringException::class);
        $today = new \DateTime('wrong date');
    }

    public function testCreateFromFormat()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', '2001-02-03 04:05:06');
        $this->assertInstanceOf(DateTime::class, $datetime);

        $this->assertSame('2001-02-03T04:05:06.000+00:00', $datetime->format(DateTime::RFC3339_EXTENDED));

        $datetime = DateTime::createFromFormat('Y-m-d', 'wrong date');
        $this->assertFalse($datetime);
    }

    public function testCreateFromImmutable()
    {
        $datetime = DateTime::createFromImmutable(new \DateTimeImmutable('2001-02-03 04:05:06'));
        $this->assertInstanceOf(DateTime::class, $datetime);

        $this->assertSame('2001-02-03T04:05:06.000+00:00', $datetime->format(DateTime::RFC3339_EXTENDED));
    }

    public function testCreateFromInterface()
    {
        $datetime = DateTime::createFromInterface(new \DateTime('2001-02-03 04:05:06'));
        $this->assertInstanceOf(DateTime::class, $datetime);

        $this->assertSame('2001-02-03T04:05:06.000+00:00', $datetime->format(DateTime::RFC3339_EXTENDED));
    }

    public function testCreateFromTimestamp()
    {
        $datetime = DateTime::createFromTimestamp(time());
        $this->assertInstanceOf(DateTime::class, $datetime);
        $today = new \DateTime('now');
        $this->assertSame($today->format(\DateTime::RFC3339), $datetime->format(DateTime::RFC3339));
    }

    public function testToString()
    {
        $datetime = new DateTime();
        $this->assertSame((new \DateTime('now'))->format('Y-m-d\TH:i:sP'), (string) $datetime);
    }
}
