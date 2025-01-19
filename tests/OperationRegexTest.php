<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operation\RegexOperation;

final class OperationRegexTest extends Base
{
    public function testMatchRegexExpression()
    {
        $actualValue = 'Today is 16/07/2023. Hello World';
        $toCompare = '#\d{2}/\d{2}/\d{4}#';

        $operator = new RegexOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }
}
