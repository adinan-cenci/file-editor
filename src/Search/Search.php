<?php

namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;
use AdinanCenci\FileEditor\Search\Condition\AndConditionGroup;
use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;
use AdinanCenci\FileEditor\Search\Iterator\MetadataIterator;

class Search implements ConditionGroupInterface
{
    /**
     * @var AdinanCenci\FileEditor\File
     *   The file subject to this search.
     */
    protected File $file;

    /**
     * @var AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface
     *   The main condition group.
     */
    protected ConditionGroupInterface $mainConditionGroup;

    /**
     * @var AdinanCenci\FileEditor\Search\Order;
     *   Object to order the results.
     */
    protected Order $order;

    /**
     * Constructor.
     *
     * @param AdinanCenci\FileEditor\File
     *   The file subject to this search.
     * @param string $operator
     *   The logic operator: "AND" or "OR".
     */
    public function __construct(File $file, string $operator = 'AND')
    {
        $this->file = $file;
        $this->mainConditionGroup = $operator == 'OR'
            ? new OrConditionGroup()
            : new AndConditionGroup();
        $this->order = new Order();
    }

    /**
     * Executes the search and returns the ordered results.
     *
     * @return string[]
     *   The lines of the file that match our criteria, indexed by their
     *   position in the file.
     */
    public function find(): array
    {
        $results = $this->retrieveAndOrder();
        array_walk($results, function (&$item) {
            $item = $item->content;
        });

        return $results;
    }

    /**
     * Adds a new criteria to order the results by.
     *
     * @param array|string $property
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     *
     * @return AdinanCenci\FileEditor\Search\Search
     *   Returns itself.
     */
    public function orderBy(mixed $property, string $direction = 'ASC'): Search
    {
        $this->order->orderBy($property, $direction);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate($data): bool
    {
        return $this->mainConditionGroup->evaluate($data);
    }

    /**
     * {@inheritDoc}
     */
    public function condition($propertyPath, $valueToCompare, string $operator = '='): self
    {
        $this->mainConditionGroup->condition($propertyPath, $valueToCompare, $operator);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function andConditionGroup(): AndConditionGroup
    {
        return $this->mainConditionGroup->andConditionGroup();
    }

    /**
     * {@inheritDoc}
     */
    public function orConditionGroup(): OrConditionGroup
    {
        return $this->mainConditionGroup->orConditionGroup();
    }

    /**
     * Instantiate an iterator object.
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\MetadataIterator
     *   The iterator object.
     */
    protected function getIterator(): \Iterator
    {
        return new MetadataIterator($this->file->fileName);
    }

    /**
     * Executes the search and returns the ordered results.
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface[]
     *   An array of matching lines, each inside a metadata wrapper.
     */
    protected function retrieveAndOrder(): array
    {
        $results = [];
        $iterator = $this->getIterator();

        foreach ($iterator as $line => $object) {
            if ($object && $this->evaluate($object)) {
                $results[ $line ] = $object;
            }
        }

        $this->order->order($results);
        return $results;
    }
}
