<?php

namespace AdinanCenci\FileEditor\Search\Condition;

use AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface;

interface ConditionInterface
{
    /**
     * Will determine if $data meets the condition.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface $data
     *   The data to be evaluated.
     *
     * @return bool
     *   Trues if it meets the condition, false if it doesn't.
     */
    public function evaluate(DataWrapperInterface $data): bool;
}
