<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface;

interface SortCriteriaInterface
{
    /**
     * Method to compare two objects.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface $item1
     *   Item to compare.
     * @param AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface $item2
     *   Item to compare.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins..
     */
    public function sort(DataWrapperInterface $item1, DataWrapperInterface $item2): int;
}
