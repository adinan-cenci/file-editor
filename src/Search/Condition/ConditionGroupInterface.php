<?php 
namespace AdinanCenci\FileEditor\Search\Condition;

interface ConditionGroupInterface extends ConditionInterface 
{
    /**
     * Add a new condition to this group.
     * Returns self to chain in other methods.
     * 
     * @param array|string[] $property Either a simple string or an array of strings to reach nested properties.
     * @param mixed $valueToCompare
     * @param string $operatorId
     * @return self
     */
    public function condition($property, $valueToCompare, string $operatorId = '=') : ConditionGroupInterface;

    /**
     * Adds a new condition group ( nested inside this one ).
     * Returns the new condition group.
     * 
     * @return ConditionGroupInterface
     */
    public function andConditionGroup() : ConditionGroupInterface;

    /**
     * Adds a new condition group ( nested inside this one ).
     * Returns the new condition group.
     * 
     * @return ConditionGroupInterface
     */
    public function orConditionGroup() : ConditionGroupInterface;
}
