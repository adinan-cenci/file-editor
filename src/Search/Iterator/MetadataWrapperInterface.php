<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * Wrapper to extract metadata from the lines.
 */
interface MetadataWrapperInterface
{
    public function __get(string $propertyName);

    public function __isset(string $propertyName);

    /**
     * Retrieves the value we want from $data, given the path.
     *
     * @param string|string[] $propertyPath
     *   A path to extract the value from $data.
     *
     * @return string|int|float|bool|null|array|\stdClass
     *   Tha value extracted from $data.
     */
    public function getValue($propertyPath);
}
