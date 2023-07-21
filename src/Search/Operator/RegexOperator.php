<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class RegexOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        return preg_match($this->valueToCompare, $this->actualValue);
    }
}
