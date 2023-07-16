<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operator\GreaterThanOperator;

final class OperatorGreaterThanTest extends Base
{
    public function testCompareToLesserNumber() 
    {
        $actualValue = 10;
        $toCompare = 5;

        $operator = new GreaterThanOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareToGreaterNumber() 
    {
        $actualValue = 5;
        $toCompare = 10;

        $operator = new GreaterThanOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareNonNumericalValue() 
    {
        $actualValue = 5;
        $toCompare = 'foobar';

        $this->expectException('InvalidArgumentException');
        $operator = new GreaterThanOperator($actualValue, $toCompare);
    }
}
