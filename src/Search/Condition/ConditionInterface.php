<?php 
namespace AdinanCenci\FileEditor\Search\Condition;

interface ConditionInterface 
{
    /**
     * Will determine if $data passes the condition based on its properties.
     * 
     * @param object|array $data
     * @return bool
     */
    public function evaluate($data) : bool;
}
