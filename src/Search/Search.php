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
    }

    /**
     * Find the results.
     *
     * It will iterate through the file and return the objects that match the
     * specified criteria.
     *
     * @return string[]
     *   The lines of the file that match the criteria, indexed by their
     *   position in the file.
     */
    public function find(): array
    {
        $results = $this->search();
        array_walk($results, function (&$item) {
            $item = $item->content;
        });

        return $results;
    }

    public function search(): array
    {
        $results = [];
        $iterator = new MetadataIterator($this->file->fileName);

        foreach ($iterator as $line => $object) {
            if ($object && $this->evaluate($object)) {
                $results[ $line ] = $object;
            }
        }

        return $results;
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
}
