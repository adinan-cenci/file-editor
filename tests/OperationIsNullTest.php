<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operation\IsNullOperation;

final class OperationIsNullTest extends Base
{
    public function testCompareNullToNull()
    {
        $actualValue = null;

        $operator = new IsNullOperation($actualValue, null);
        $this->assertTrue($operator->matches());
    }

    public function testCompareFalseToNull()
    {
        $actualValue = false;

        $operator = new IsNullOperation($actualValue, null);
        $this->assertFalse($operator->matches());
    }
}
