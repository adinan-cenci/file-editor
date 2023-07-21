<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\Search\Operator\EqualOperator;

final class OperatorEqualsTest extends Base
{
    // STRINGS
    public function testCompareStrings() 
    {
        $actualValue = 'Highland Glory';
        $toCompare = 'Highland Glory';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareStringsCaseInsensitive() 
    {
        $actualValue = 'Highland Glory';
        $toCompare = 'HIGHLAND GLORY';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareNullToEmptyString() 
    {
        $actualValue = null;
        $toCompare = '';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareFalseToEmptyString() 
    {
        $actualValue = false;
        $toCompare = '';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareIntegerToNumericalString() 
    {
        $actualValue = 5;
        $toCompare = '5';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareZeroToEmptyString() 
    {
        $actualValue = 0;
        $toCompare = '';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareTrueToZeroString() 
    {
        $actualValue = true;
        $toCompare = '0';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareTrueToOneString() 
    {
        $actualValue = true;
        $toCompare = '1';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    // ARRAYS
    public function testCompareArrayToString() 
    {
        $actualValue = ['Highland Glory'];
        $toCompare = 'Highland Glory';

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareArrayToArray() 
    {
        $actualValue = ['Highland Glory'];
        $toCompare = ['Highland Glory'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareArrayToArrayCaseInsensitive() 
    {
        $actualValue = ['Highland Glory'];
        $toCompare = ['HIGHLAND GLORY'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareIntersectingArrays() 
    {
        $actualValue = ['Highland Glory', 'Gloryhammer'];
        $toCompare = ['Highland Glory'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());

        //----------------------------

        $actualValue = ['Highland Glory'];
        $toCompare = ['Highland Glory', 'Gloryhammer'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertFalse($operator->matches());
    }

    public function testCompareUnorderedNumericalArrays() 
    {
        $actualValue = ['Highland Glory', 'Gloryhammer'];
        $toCompare = ['Gloryhammer', 'Highland Glory'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }

    public function testCompareUnorderedAssociativeArrays() 
    {
        $actualValue = ['first' => 'Highland Glory', 'second' => 'Gloryhammer'];
        $toCompare = ['second' => 'Gloryhammer', 'first' => 'Highland Glory'];

        $operator = new EqualOperator($actualValue, $toCompare);
        $this->assertTrue($operator->matches());
    }
}
