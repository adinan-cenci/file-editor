<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;

/**
 * Order results based on a seed.
 */
class SeedSort implements SortCriteriaInterface
{
    /**
     * @var string
     *   Seed.
     */
    protected string $seed;

    /**
     * @param string $seed
     *   Seed.
     */
    public function __construct(string $seed)
    {
        $this->seed = $seed;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(MetadataWrapperInterface $item1, MetadataWrapperInterface $item2): int
    {
        $str1 = $item1 . $this->seed;
        $str2 = $item2 . $this->seed;
        return Compare::compareStrings(md5($str1), md5($str2), 'ASC');
    }
}
