<?php

namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;
use AdinanCenci\FileEditor\Search\Condition\AndConditionGroup;
use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;
use AdinanCenci\FileEditor\Search\Iterator\DataIterator;
use AdinanCenci\FileEditor\Search\Order\Order;

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
     * @var AdinanCenci\FileEditor\Search\Order\Order;
     *   Object to order the results.
     */
    protected Order $order;

    /**
     * @var callcable[]
     *   Array of callbacks to extract metadata from search results.
     */
    protected array $metadataGetters = [];

    /**
     * @var callcable[]
     *   Array of callbacks to set metadata for search results.
     */
    protected array $metadataSetters = [];

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

        $this->setMetadataSetter('length', function ($iterator) {
            return $iterator->currentContent
                ? strlen($iterator->currentContent)
                : 0;
        });

        $this->setMetadataSetter('lineNumber', function ($iterator) {
            return $iterator->currentLine;
        });

        // Alias to lineNumber.
        $this->setMetadataGetter('position', function ($dataWrapper) {
            return $dataWrapper->metadata->lineNumber;
        });
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
     * Executes the search and returns the ordered results.
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface[]
     *   An array of matching lines, each inside a metadata wrapper.
     */
    public function retrieveAndOrder(): array
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

    /**
     * Adds a new criteria to order the results by a specified property.
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
     * Adds a new criteria to order the results randomly.
     *
     * @param null|string $seed
     *   If informed, the seed will be used to order the results.
     *   The items will be order the same every time.
     *
     * @return AdinanCenci\FileEditor\Search\Search
     *   Return itself.
     */
    public function orderRandomly(?string $seed = null): Search
    {
        $this->order->orderRandomly($seed);
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

    public function setMetadataGetter(string $property, mixed $callable)
    {
        $this->metadataGetters[$property] = $callable;
        return $this;
    }

    public function setMetadataSetter(string $property, mixed $callable)
    {
        $this->metadataSetters[$property] = $callable;
        return $this;
    }

    /**
     * Instantiate an iterator object.
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\DataIterator
     *   The iterator object.
     */
    protected function getIterator(): \Iterator
    {
        return new DataIterator($this->file->fileName, $this->metadataGetters, $this->metadataSetters);
    }
}
