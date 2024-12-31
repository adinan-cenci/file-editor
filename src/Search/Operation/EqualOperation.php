<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class EqualOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        return $this->leftOperand == $this->rightOperand;
    }
}
