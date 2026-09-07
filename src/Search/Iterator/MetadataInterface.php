<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * Metadata wrapper.
 */
interface MetadataInterface
{
    public function __isset(string $data): bool;

    public function __get(string $data): mixed;
}
