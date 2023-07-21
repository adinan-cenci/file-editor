<?php 
namespace AdinanCenci\FileEditor\Search\Condition;

class OrConditionGroup extends AndConditionGroup implements ConditionInterface, ConditionGroupInterface 
{
    /**
     * @inheritDoc
     */
    public function evaluate($data) : bool
    {
        foreach ($this->conditions as $condition) {
            if ($condition->evaluate($data)) {
                return true;
            }
        }

        return false;
    }
}
