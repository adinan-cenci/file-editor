<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operation\LikeOperation;

final class OperationLikesTest extends Base
{
    public function testCompareStrings()
    {
        $actualValue = 'Highland Glory';
        $toCompare = 'Highland Glory';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareStringsCaseInsensitive()
    {
        $actualValue = 'Highland Glory';
        $toCompare = 'HIGHLAND GLORY';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareSubstring()
    {
        $actualValue = 'Highland Glory';
        $toCompare = 'glory';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareStringToArray()
    {
        $actualValue = 'Highland Glory';
        $toCompare = ['glory'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToString()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = 'Highland Glory';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToStringCaseInsensitive()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = 'HIGHLAND GLORY';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToSubstring()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = 'glory';

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToArray()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = ['Highland Glory'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToArraySubstring()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = ['Glory'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToArrayCaseInsensitive()
    {
        $actualValue = ['Highland Glory'];
        $toCompare = ['HIGHLAND GLORY'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareIntersectingArrays()
    {
        $actualValue = ['Highland Glory', 'Gloryhammer'];
        $toCompare = ['Highland Glory'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());

        //---------

        $actualValue = ['Highland Glory', 'Gloryhammer'];
        $toCompare = ['hammer'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertTrue($operator->matches());

        //---------

        $actualValue = ['Highland Glory'];
        $toCompare = ['Highland Glory', 'Gloryhammer'];

        $operator = new LikeOperation($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }
}
