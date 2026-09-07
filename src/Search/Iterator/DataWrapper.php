<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * Wrapper to extract metadata from the subject of our search.
 */
class DataWrapper implements DataWrapperInterface
{
    /**
     * @var AdinanCenci\FileEditor\Search\Iterator\MetadataInterface
     *   Metadata object.
     */
    protected ?MetadataInterface $metadata;

    /**
     * Constructor.
     *
     * @param string $content
     *   The contents of the line.
     */
    public function __construct(protected string $content)
    {
    }

    public function __toString()
    {
        return $this->content;
    }

    /**
     * Sets the metadata object.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\MetadataInterface $metadata
     *   Metadata
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface
     *   Itself.
     */
    public function setMetadata(MetadataInterface $metadata): DataWrapperInterface
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Returns data from our subject, actual or computed.
     *
     * @param string $propertyName
     *   The data we want to retrieve.
     *
     * @return mixed|null
     */
    public function __get(string $propertyName)
    {
        if (isset($this->{$propertyName})) {
            return $this->{$propertyName};
        }

        return null;
    }

    /**
     * Check if property from our subject is set, actual or computed.
     *
     * @param string $propertyName
     *   The property we want to check if it is set.
     */
    public function __isset(string $propertyName)
    {
        $value = $this->__get($propertyName);
        return !is_null($value);
    }

    /**
     * Retrieves the value we want from $data, given the path.
     *
     * @param string|string[] $propertyPath
     *   A path to extract the value from $data.
     *
     * @return string|int|float|bool|null|array|\stdClass
     *   Tha value extracted from $data.
     */
    public function getValue($propertyPath)
    {
        $propertyPath = (array) $propertyPath;
        $data = $this;

        foreach ($propertyPath as $part) {
            if ($part == '@metadata' && $this->metadata) {
                $data = $this->metadata;
            } elseif (isset($data->{$part})) {
                $data = $data->{$part};
            } else {
                return null;
            }
        }

        return $data;
    }
}
