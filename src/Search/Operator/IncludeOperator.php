<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class IncludeOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        if (is_array($this->actualValue) && is_scalar($this->valueToCompare)) {
            return in_array($this->valueToCompare, $this->actualValue);
        }

        if (is_array($this->actualValue) && is_array($this->valueToCompare)) {
            return count(array_intersect($this->actualValue, $this->valueToCompare)) == count($this->valueToCompare);
        }

        if (is_scalar($this->actualValue) && is_scalar($this->valueToCompare)) {
            return $this->actualValue == $this->valueToCompare;
        }

        if (is_scalar($this->actualValue) && is_array($this->valueToCompare)) {
            return in_array($this->actualValue, $this->valueToCompare);
        }

        return false;
    }
}
