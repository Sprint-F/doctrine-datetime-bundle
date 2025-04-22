<?php


namespace SprintF\Bundle\Datetime\Tests\Unit\Value;

use SprintF\Bundle\Datetime\Tests\Support\UnitTester;

class DateTest extends \Codeception\Test\Unit
{

    protected UnitTester $tester;

    protected function _before()
    {
    }

    // tests
    public function testTrivial()
    {
        $this->assertTrue(true);
    }
}
