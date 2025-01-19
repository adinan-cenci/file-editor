<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class IncludeOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        if (is_array($this->leftOperand) && is_scalar($this->rightOperand)) {
            return in_array($this->rightOperand, $this->leftOperand);
        }

        if (is_array($this->leftOperand) && is_array($this->rightOperand)) {
            return count(array_intersect($this->leftOperand, $this->rightOperand)) == count($this->rightOperand);
        }

        if (is_scalar($this->leftOperand) && is_scalar($this->rightOperand)) {
            return $this->leftOperand == $this->rightOperand;
        }

        if (is_scalar($this->leftOperand) && is_array($this->rightOperand)) {
            return in_array($this->leftOperand, $this->rightOperand);
        }

        return false;
    }
}
