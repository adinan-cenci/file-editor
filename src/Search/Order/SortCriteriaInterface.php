<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;

interface SortCriteriaInterface
{
    /**
     * Method to compare two objects.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface $item1
     *   Item to compare.
     * @param AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface $item2
     *   Item to compare.
     *
     * @return int
     *   0 = equals,
     *   1 = $value1 wins
     *  -1 = $value2 wins..
     */
    public function sort(MetadataWrapperInterface $item1, MetadataWrapperInterface $item2): int;
}
