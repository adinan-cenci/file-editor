<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class IsNullOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        return is_null($this->actualValue);
    }

    protected function normalizeScalar($data) 
    {
        if (is_null($data)) {
            return null;
        }

        return parent::normalizeScalar($data);
    }
}
