<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;

class PropertySort implements SortCriteriaInterface
{
    /**
     * @var array
     *   The property to order by.
     */
    protected array $property;

    /**
     * @var string
     *   Ascending or descending.
     */
    protected string $direction;

    /**
     * Constructor.
     *
     * @param array|string $property
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     */
    public function __construct(mixed $property, string $direction = 'ASC')
    {
        $this->property = (array) $property;
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(MetadataWrapperInterface $item1, MetadataWrapperInterface $item2): int
    {
        $value1 = $item1->getValue($this->property);
        $value2 = $item2->getValue($this->property);

        return Compare::compare($value1, $value2, $this->direction);
    }
}
