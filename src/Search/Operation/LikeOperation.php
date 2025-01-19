<?php

namespace AdinanCenci\FileEditor\Search\Operation;

class LikeOperation extends OperationBase implements OperationInterface
{
    /**
     * {@inheritDoc}
     */
    public function compare(): bool
    {
        if (is_scalar($this->leftOperand) && is_scalar($this->rightOperand)) {
            return substr_count($this->leftOperand, $this->rightOperand);
        }

        if (is_array($this->leftOperand) && is_scalar($this->rightOperand)) {
            foreach ($this->leftOperand as $av) {
                if (is_scalar($av) && substr_count($av, $this->rightOperand)) {
                    return true;
                }
            }

            return false;
        }

        if (is_scalar($this->leftOperand) && is_array($this->rightOperand)) {
            foreach ($this->rightOperand as $cv) {
                if (is_scalar($cv) && substr_count($this->leftOperand, $cv)) {
                    return true;
                }
            }

            return false;
        }

        if (is_array($this->leftOperand) && is_array($this->rightOperand)) {
            $matches = 0;
            foreach ($this->rightOperand as $cv) {
                foreach ($this->leftOperand as $av) {
                    $matches += is_scalar($cv) && is_scalar($av) && substr_count($av, $cv);
                }
            }

            return $matches >= count($this->rightOperand);
        }

        return false;
    }
}
