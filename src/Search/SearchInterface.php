<?php

namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;

interface SearchInterface extends ConditionGroupInterface
{
    /**
     * Executes the search and returns the ordered results.
     *
     * @return array
     *   The entries of the file that match our criteria, indexed by their
     *   position in the file.
     */
    public function find(): array;

    /**
     * Executes the search and returns the ordered results.
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface[]
     *   An array of matching entries, each inside a data wrapper object.
     */
    public function retrieveAndOrder(): array;

    /**
     * Adds a new criteria to order the results by a specified property.
     *
     * @param array|string $property
     *   The property to order by.
     * @param string $direction
     *   Ascending or descending.
     *
     * @return AdinanCenci\FileEditor\Search\SearchInterface
     *   Returns itself.
     */
    public function orderBy(mixed $property, string $direction = 'ASC'): SearchInterface;

    /**
     * Adds a new criteria to order the results randomly.
     *
     * @param null|string $seed
     *   If informed, the seed will be used to order the results.
     *   The items will be order the same every time.
     *
     * @return AdinanCenci\FileEditor\Search\SearchInterface
     *   Return itself.
     */
    public function orderRandomly(?string $seed = null): SearchInterface;

    /**
     * Register a eager metadata getter.
     *
     * @param string $property
     *   The metadata name.
     * @param callable $callable
     *   A closure.
     *
     * @return AdinanCenci\FileEditor\Search\SearchInterface
     *   Return itself.
     */
    public function setMetadataEagerGetter(string $property, mixed $callable): SearchInterface;

    /**
     * Register a lazy metadata getter.
     *
     * @param string $property
     *   The metadata name.
     * @param callable $callable
     *   A closure.
     *
     * @return AdinanCenci\FileEditor\Search\SearchInterface
     *   Return itself.
     */
    public function setMetadataLazyGetter(string $property, mixed $callable): SearchInterface;
}
