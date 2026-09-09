<?php

namespace AdinanCenci\FileEditor\Search\Order;

use AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface;

class PropertySort implements SortCriteriaInterface
{
    /**
     * @var array
     *   The property to order by.
     */
    protected array $propertyPath;

    /**
     * @var string
     *   Ascending or descending.
     */
    protected string $direction;

    /**
     * Constructor.
     *
     * @param array|string $propertyPath
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     */
    public function __construct(mixed $propertyPath, string $direction = 'ASC')
    {
        $this->propertyPath = (array) $propertyPath;
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function sort(DataWrapperInterface $item1, DataWrapperInterface $item2): int
    {
        $value1 = $item1->getValue($this->propertyPath);
        $value2 = $item2->getValue($this->propertyPath);

        return Compare::compare($value1, $value2, $this->direction);
    }

    /**
     * Returns the property path.
     *
     * @return array
     *   The property path.
     */
    public function getPropertyPath(): array
    {
        return $this->propertyPath;
    }
}
