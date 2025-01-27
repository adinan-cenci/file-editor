<?php

namespace AdinanCenci\FileEditor\Search\Order;

/**
 * Orders the search results.
 */
class Order
{
    /**
     * @var AdinanCenci\FileEditor\Search\Order\SortCriteriaInterface[]
     *   Criteria to order the results by.
     */
    protected array $criteria = [];

    /**
     * Adds a new criteria to order the results by a specified property.
     *
     * @param array|string $property
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     *
     * @return AdinanCenci\FileEditor\Search\Order\Order
     *   Return itself.
     */
    public function orderBy(mixed $property, string $direction = 'ASC'): Order
    {
        if (preg_match('#RAND\(([^\)]*)\)#', $property, $matches)) {
            $this->orderRandomly($matches[1] ?? null);
            return $this;
        }

        $this->criteria[] = new PropertySort($property, $direction);
        return $this;
    }

    /**
     * Adds a new criteria to order the results randomly.
     *
     * @param null|string $seed
     *   If informed, the seed will be used to order the results.
     *   The items will be order the same every time.
     *
     * @return AdinanCenci\FileEditor\Search\Order\Order
     *   Return itself.
     */
    public function orderRandomly(?string $seed = null): Order
    {
        $this->criteria[] = $seed
            ? new SeedSort($seed)
            : new RandomSort();

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
            uasort($results, [$criteria, 'sort']);
        }
    }
}
