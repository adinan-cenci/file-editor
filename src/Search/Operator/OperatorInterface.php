<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

interface OperatorInterface 
{
    /**
     * @param mixed $actualValue
     * @param mixed $valueToCompare
     */
    public function __construct($actualValue, $valueToCompare);

    /**
     * Compares the values and returns weather they match or not.
     * 
     * @return bool
     */
    public function matches() : bool;
}
