<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class IsNullOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        return is_null($this->leftOperand);
    }

    protected function normalizeScalar($data)
    {
        if (is_null($data)) {
            return null;
        }

        return parent::normalizeScalar($data);
    }
}
