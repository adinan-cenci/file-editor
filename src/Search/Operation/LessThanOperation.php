<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class LessThanOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        if (! is_numeric($this->leftOperand)) {
            return false;
        }

        return $this->leftOperand < $this->rightOperand;
    }

    /**
     * {@inheritDoc}
     */
    protected function validateValueToCompare(): void
    {
        if (! is_numeric($this->rightOperand)) {
            throw new \InvalidArgumentException($this->invalidDataError('LESS THAN', 'numeric', gettype($this->rightOperand)), \E_USER_ERROR);
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
