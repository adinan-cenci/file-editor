<?php

namespace AdinanCenci\FileEditor\Search\Condition;

interface ConditionInterface
{
    /**
     * Will determine if $data meets the condition.
     *
     * @param object|array $data
     *   The data to be evaluated.
     *
     * @return bool
     *   Trues if it passes, false if it does not.
     */
    public function evaluate($data): bool;
}
