<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operator\IsNullOperator;

final class OperatorIsNullTest extends Base
{
    public function testCompareNullToNull() 
    {
        $actualValue = null;

        $operator = new IsNullOperator($actualValue, null);
        $this->assertTrue($operator->matches());
    }

    public function testCompareFalseToNull() 
    {
        $actualValue = false;

        $operator = new IsNullOperator($actualValue, null);
        $this->assertFalse($operator->matches());
    }
}
