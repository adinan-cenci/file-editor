<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class BetweenOperation extends LessThanOperation implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        if (! is_numeric($this->leftOperand)) {
            return false;
        }

        $min = reset($this->rightOperand);
        $max = end($this->rightOperand);

        return $this->leftOperand > $min && $this->leftOperand < $max;
    }

    /**
     * {@inheritDoc}
     */
    protected function validateValueToCompare(): void
    {
        if (! is_array($this->rightOperand)) {
            throw new \InvalidArgumentException($this->invalidDataError('BETWEEN', 'array', gettype($this->rightOperand)));
        }

        if (
            count($this->rightOperand) < 2 ||
            (!is_numeric(reset($this->rightOperand)) || !is_numeric(end($this->rightOperand)))
        ) {
            throw new \InvalidArgumentException($this->invalidDataError('BETWEEN', 'array with two numeric values', ''));
        }
    }
}
