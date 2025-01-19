<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * Wrapper to extract metadata from the subject of our search.
 */
class MetadataWrapper implements MetadataWrapperInterface
{
    /**
     * @var int
     *   The position of the line within the file.
     */
    protected int $position;

    /**
     * @var string
     *   The contents of the line.
     */
    protected string $content;

    /**
     * Constructor.
     *
     * @param int $position
     *   The position of the line within the file.
     * @param string $content
     *   The contents of the line.
     */
    public function __construct(int $position, string $content)
    {
        $this->position = $position;
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
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
        $methodName = 'compute' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->fallbackCompute($propertyName);
    }

    /**
     * Fallback compute method.
     *
     * If there is no method for the specified property.
     *
     * @param string $propertyName
     *   The property we want to retrieve.
     */
    protected function fallbackCompute(string $propertyName)
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
        $methodName = 'isset' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->fallbackIsset($propertyName);
    }

    /**
     * Fallback isset method.
     *
     * If there is no method for the specified property.
     *
     * @param string $propertyName
     *   The property we want to check if it is set.
     */
    protected function fallbackIsset(string $propertyName)
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
            if (isset($data->{$part})) {
                $data = $data->{$part};
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Returns the lenght of the line.
     */
    protected function computeLength()
    {
        return strlen($this->content);
    }

    /**
     * Returns the position of the line in the file.
     */
    protected function computeLineNumber()
    {
        return $this->position;
    }
}
