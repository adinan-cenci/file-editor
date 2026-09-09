<?php

namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;
use AdinanCenci\FileEditor\Search\Condition\AndConditionGroup;
use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;
use AdinanCenci\FileEditor\Search\Iterator\DataIterator;
use AdinanCenci\FileEditor\Search\Order\Order;

class Search implements SearchInterface
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
     *   They will receive the data being iterated upon, from which they will
     *   extract information. Eager getters execute when iterated upon.
     */
    protected array $metadataEagerGetters = [];

    /**
     * @var callcable[]
     *   Array of callbacks to extract metadata from search results.
     *   They will receive the data being iterated upon, from which they will
     *   extract information. Lazy getters execute when called.
     */
    protected array $metadataLazyGetters = [];

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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function retrieveAndOrder(): array
    {
        $results = [];
        $this->registerBuiltInMetadataGetters();
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
     * {@inheritdoc}
     */
    public function orderBy(mixed $property, string $direction = 'ASC'): SearchInterface
    {
        $this->order->orderBy($property, $direction);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function orderRandomly(?string $seed = null): SearchInterface
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

    /**
     * {@inheritdoc}
     */
    public function accumulateProperties(array &$properties = []): void
    {
        $this->mainConditionGroup->accumulateProperties($properties);
        $this->order->accumulateProperties($properties);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataEagerGetter(string $property, mixed $callable): SearchInterface
    {
        $this->metadataEagerGetters[$property] = $callable;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataLazyGetter(string $property, mixed $callable): SearchInterface
    {
        $this->metadataLazyGetters[$property] = $callable;
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
        return new DataIterator($this->file->filename, $this->metadataEagerGetters, $this->metadataLazyGetters);
    }

    /**
     * Registers built-in metadata getters.
     *
     * Also serves as example of how to use them.
     */
    protected function registerBuiltInMetadataGetters(): void
    {
        $properties = [];
        $this->accumulateProperties($properties);

        if (in_array(['@metadata', 'length'], $properties)) {
            $this->setMetadataEagerGetter('length', function ($iterator, $dataWrapper) {
                return $iterator->currentContent
                    ? strlen(rtrim($iterator->currentContent, "\n"))
                    : 0;
            });
        }

        if (in_array(['@metadata', 'lineNumber'], $properties)) {
            $this->setMetadataEagerGetter('lineNumber', function ($iterator, $dataWrapper) {
                return $iterator->currentLine;
            });
        }
    }
}
