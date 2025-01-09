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
     *
     * @return AdinanCenci\FileEditor\Search\Order
     *   Return itself.
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
     *   The search results to order.
     */
    public function order(array &$results)
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
    protected function sortAtRandom($item1, $item2): int
    {
        return rand(1, 1000) % 2 == 0
            ? -1
            :  1;
    }

    /**
     * Sorts by property.
     *
     * @param string|array $property
     *   Path to the property.
     * @param string $direction
     *   Ascending or descending.
     *
     * @return closure
     *   A function to pass to uasort().
     */
    protected function sortByProperty($property, $direction)
    {
        return function ($item1, $item2) use ($property, $direction) {
            $value1 = $item1->getValue($property);
            $value2 = $item2->getValue($property);

            return $this->compare($value1, $value2, $direction);
        };
    }

    /**
     * Compare two values.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    protected function compare($value1, $value2, $direction): int
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

        if (is_null($value1) || is_null($value2)) {
            return $this->compareNulls($value1, $value2, $direction);
        }

        return 0;
    }

    /**
     * Compare null values with other types.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    protected function compareNulls($value1, $value2, $direction): int
    {
        return $direction == 'ASC'
            ? (!is_null($value1) ?  1 : -1)
            : (!is_null($value1) ? -1 :  1);
    }

    /**
     * Compare numeric values.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    protected function compareNumbers($value1, $value2, $direction): int
    {
        return $direction == 'ASC'
            ? ($value1 > $value2 ?  1 : -1)
            : ($value1 > $value2 ? -1 :  1);
    }

    /**
     * Compare strings.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    protected function compareStrings($value1, $value2, $direction): int
    {
        $v = strcmp($value1, $value2);

        return $direction == 'ASC'
            ? ($v > 0 ?  1 : -1)
            : ($v > 0 ? -1 :  1);
    }

    /**
     * Compare arrays.
     *
     * @param mixed $value1
     *   Value 1.
     * @param mixed $value2
     *   Value 2.
     * @param string $direction
     *  Ascending or descending.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins.
     */
    protected function compareArrays($value1, $value2, $direction): int
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

        return $t1 == $t2
            ? 0
            : $this->compareNumbers($t1, $t2, $direction);
    }
}
