<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

class LikeOperator extends OperatorBase implements OperatorInterface 
{
    /**
     * @inheritDoc
     */
    public function matches() : bool
    {
        if (is_scalar($this->actualValue) && is_scalar($this->valueToCompare)) {
            return substr_count($this->actualValue, $this->valueToCompare);
        }

        if (is_array($this->actualValue) && is_scalar($this->valueToCompare)) {
            foreach ($this->actualValue as $av) {
                if (is_scalar($av) && substr_count($av, $this->valueToCompare)) {
                    return true;
                }
            }

            return false;
        }

        if (is_scalar($this->actualValue) && is_array($this->valueToCompare)) {
            foreach ($this->valueToCompare as $cv) {
                if (is_scalar($cv) && substr_count($this->actualValue, $cv)) {
                    return true;
                }
            }

            return false;
        }

        if (is_array($this->actualValue) && is_array($this->valueToCompare)) {
            $matches = 0;
            foreach ($this->valueToCompare as $cv) {
                foreach ($this->actualValue as $av) {
                    $matches += is_scalar($cv) && is_scalar($av) && substr_count($av, $cv);
                }
            }

            return $matches >= count($this->valueToCompare);
        }

        return false;
    }
}
