<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * Wrapper to extract metadata from the lines.
 *
 * @property string $content
 *   The content of the line.
 * @property int $position
 *   The positon of the line in the file.
 * @property int $length
 *   The length of the line.
 */
class MetadataWrapper
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

    public function __get(string $propertyName)
    {
        $methodName = 'compute' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->fallbackCompute($propertyName);
    }

    public function __isset(string $propertyName)
    {
        $methodName = 'isset' . ucfirst($propertyName);
        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        }

        return $this->fallbackIsset($propertyName);
    }

    protected function computeLength()
    {
        return strlen($this->content);
    }

    protected function computeLineNumber()
    {
        return $this->position;
    }

    protected function fallbackCompute(string $propertyName)
    {
        if (isset($this->{$propertyName})) {
            return $this->{$propertyName};
        }

        return null;
    }

    protected function fallbackIsset(string $propertyName)
    {
        $value = $this->__get($propertyName);
        return !is_null($value);
    }
}
