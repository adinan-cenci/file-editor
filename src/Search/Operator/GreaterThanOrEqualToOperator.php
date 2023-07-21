<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class GreaterThanOrEqualToOperator extends LessThanOperator implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        if (! is_numeric($this->actualValue)) {
            return false;
        }

        return $this->actualValue >= $this->valueToCompare;
    }
}
