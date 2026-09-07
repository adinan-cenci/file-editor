<?php

namespace AdinanCenci\FileEditor\Search\Condition;

interface ConditionGroupInterface extends ConditionInterface
{
    /**
     * Add a new condition to this group.
     *
     * @param string[] $propertyPath
     *   A path to extract the actual value during evaluation.
     * @param mixed $valueToCompare
     *   The value for comparison.
     * @param string $operator
     *   The operator.
     *
     * @return AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface
     *   Returns self to chain in other methods.
     */
    public function condition($propertyPath, $valueToCompare, string $operator = '='): ConditionGroupInterface;

    /**
     * Adds a new condition group ( nested inside this one ).
     *
     * @return AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface
     *   Returns the new condition group.
     */
    public function andConditionGroup(): ConditionGroupInterface;

    /**
     * Adds a new condition group ( nested inside this one ).
     *
     * @return AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface
     *   Returns the new condition group.
     */
    public function orConditionGroup(): ConditionGroupInterface;

    /**
     * Checks if a property has been specified in the condition group.
     *
     * @param string|array $propertyPath
     *   The property path.
     *
     * @return bool
     *   True if it has.
     */
    public function propertySpecified($propertyPath): bool;
}
