<?php

namespace AdinanCenci\FileEditor\Search\Condition;

use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;

class AndConditionGroup implements ConditionInterface, ConditionGroupInterface
{
    /**
     * @param AdinanCenci\FileEditor\Search\Condition\ConditionInterface[] $conditions
     *   Array of conditions.
     */
    protected array $conditions = [];

    /**
     * {@inheritDoc}
     */
    public function condition($propertyPath, $valueToCompare, string $operator = '='): ConditionGroupInterface
    {
        $condition = new Condition($propertyPath, $valueToCompare, $operator);
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate($data): bool
    {
        foreach ($this->conditions as $condition) {
            if (! $condition->evaluate($data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function andConditionGroup(): AndConditionGroup
    {
        $group = new AndConditionGroup();
        $this->conditions[] = $group;
        return $group;
    }

    /**
     * {@inheritDoc}
     */
    public function orConditionGroup(): OrConditionGroup
    {
        $group = new OrConditionGroup();
        $this->conditions[] = $group;
        return $group;
    }
}
