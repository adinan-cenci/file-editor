<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class LessThanOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        if (! is_numeric($this->actualValue)) {
            return false;
        }

        return $this->actualValue < $this->valueToCompare;
    }

    /**
     * @inheritDoc
     */
    protected function validateValueToCompare() : void
    {
        if (! is_numeric($this->valueToCompare)) {
            throw new \InvalidArgumentException($this->invalidDataError('LESS THAN', 'numeric', gettype($this->valueToCompare)), \E_USER_ERROR);
        }
    }

    protected function normalizeScalar($data) 
    {
        if (is_numeric($data)) {
            return $data;
        }

        return parent::normalizeScalar($data);
    }
}
