<?php

namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;
use AdinanCenci\FileEditor\Search\Condition\AndConditionGroup;
use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;
use AdinanCenci\FileEditor\Search\Iterator\MetadataIterator;

/**
 * Orders the search results.
 */
class Order
{
    /**
     * @var array
     *   Criteria to order the results by.
     */
    protected $criteria = [];

    /**
     * Adds a new criteria to order the results by.
     *
     * @param array|string $property
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     */
    public function orderBy(mixed $property, string $direction = 'ASC')
    {
        $this->criteria[] = [
            $property,
            $direction
        ];

        return $this;
    }

    /**
     * Orders the search results.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\MetadataWrapper[] $results
     *   The search results.
     */
    public function order(&$results)
    {
        if (!$this->criteria) {
            return;
        }

        foreach ($this->criteria as $criteria) {
            uasort($results, $this->getSortFunction($criteria));
        }
    }

    /**
     * Given a criteria, returns a function to order the search results by.
     *
     * @param array $criteria
     *   Property and direction combo.
     *
     * @return callable
     *   A callable to order the results.
     */
    protected function getSortFunction(array $criteria): callable
    {
        list($property, $direction) = $criteria;

        return $property == 'RAND()'
            ? [$this, 'sortAtRandom']
            : $this->sortByProperty($property, $direction);
    }

    /**
     * Orders results randomly.
     *
     * @return int
     */
    protected function sortAtRandom($item1, $item2)
    {
        return rand(1, 1000) % 2 == 0
            ? -1
            :  1;
    }

    protected function sortByProperty($property, $direction)
    {
        return function ($item1, $item2) use ($property, $direction) {
            $value1 = $item1->getValue($property);
            $value2 = $item2->getValue($property);

            return $this->compare($value1, $value2, $direction);
        };
    }

    protected function compare($value1, $value2, $direction)
    {
        if ($value1 == $value2) {
            return 0;
        }

        if (is_numeric($value1) && is_numeric($value2)) {
            return $this->compareNumbers($value1, $value2, $direction);
        }

        if (is_string($value1) && is_string($value2)) {
            return $this->compareStrings($value1, $value2, $direction);
        }

        if (is_array($value1) && is_array($value2)) {
            return $this->compareArrays($value1, $value2, $direction);
        }

        return 0;
    }

    protected function compareNumbers($value1, $value2, $direction)
    {
        if ($value1 == $value2) {
            return 0;
        }

        return $direction == 'ASC'
            ? ($value1 > $value2 ?  1 : -1)
            : ($value1 > $value2 ? -1 :  1);
    }

    protected function compareStrings($value1, $value2, $direction)
    {
        $v = strcmp($value1, $value2);
        if ($v == 0) {
            return 0;
        }

        return $direction == 'ASC'
            ? ($v > 0 ? -1 :  1)
            : ($v < 0 ?  1 : -1);
    }

    protected function compareArrays($value1, $value2, $direction)
    {
        $t1 = 0;
        $t2 = 0;

        while ($value1 || $value2) {
            $v1 = $value1 ? array_shift($value1) : null;
            $v2 = $value2 ? array_shift($value2) : null;

            $c = $this->compare($v1, $v2, $direction);

            $t1 += $direction == 'ASC'  && $c > 0 ? 1 : 0;
            $t2 += $direction == 'DESC' && $c < 0 ? 1 : 0;
        }

        return $this->compareNumbers($t1, $t2, $direction);
    }
}
