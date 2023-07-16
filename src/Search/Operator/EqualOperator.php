<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class EqualOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        return $this->actualValue == $this->valueToCompare;
    }
}
