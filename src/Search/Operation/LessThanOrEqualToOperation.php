<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class LessThanOrEqualToOperation extends LessThanOperation implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        if (! is_numeric($this->leftOperand)) {
            return false;
        }

        return $this->leftOperand <= $this->rightOperand;
    }
}
