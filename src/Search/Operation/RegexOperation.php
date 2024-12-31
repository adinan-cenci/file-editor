<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class RegexOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        return preg_match($this->rightOperand, $this->leftOperand);
    }
}
