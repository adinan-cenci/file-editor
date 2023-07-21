<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operator\LessThanOperator;
use AdinanCenci\FileEditor\Search\Exception\InvalidData;

final class OperatorLessThanTest extends Base
{
    public function testCompareToGreaterNumber() 
    {
        $actualValue = 5;
        $toCompare = 10;

        $operator = new LessThanOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareToLesserNumber() 
    {
        $actualValue = 10;
        $toCompare = 5;

        $operator = new LessThanOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareToBoolean() 
    {
        $actualValue = 10;
        $toCompare = false;
        $this->expectException('InvalidArgumentException');
        $operator = new LessThanOperator($actualValue, $toCompare);
    }
}
