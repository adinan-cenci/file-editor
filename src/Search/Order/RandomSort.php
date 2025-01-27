<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;

class RandomSort implements SortCriteriaInterface
{
    /**
     * {@inheritdoc}
     */
    public function sort(MetadataWrapperInterface $item1, MetadataWrapperInterface $item2): int
    {
        return rand(1, 1000) % 2 == 0
            ? -1
            :  1;
    }
}
