<?php

namespace AdinanCenci\FileEditor\Search\Condition;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;

interface ConditionInterface
{
    /**
     * Will determine if $data meets the condition.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface $data
     *   The data to be evaluated.
     *
     * @return bool
     *   Trues if it passes, false if it doesn't.
     */
    public function evaluate(MetadataWrapperInterface $data): bool;
}
