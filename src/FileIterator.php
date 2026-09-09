<?php

namespace AdinanCenci\FileEditor;

class FileIterator implements \Iterator
{
    /**
     * @var string
     *   The absolute path to the file.
     */
    protected string $filename = '';

    /**
     * @var resource
     *   Resource to handle the file.
     */
    protected $handle = null;

    /**
     * @var null|string
     *   Content of the current line.
     */
    protected $currentContent = null;

    /**
     * @var int
     *   The current line.
     */
    protected int $currentLine = 0;

    /**
     * @param string $filename
     *   The absolute path to the file.
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function __get($var)
    {
        return isset($this->{$var})
            ? $this->{$var}
            : null;
    }

    /**
     * \Iterator::current()
     */
    public function current(): mixed
    {
        if (! $this->getHandle()) {
            return null;
        }

        return $this->currentContent;
    }

    /**
     * \Iterator::key()
     */
    public function key(): mixed
    {
        return $this->currentLine;
    }

    /**
     * \Iterator::next()
     */
    public function next(): void
    {
        if (! $this->getHandle()) {
            return;
        }

        if ($this->currentContent === false) {
            return;
        }

        $this->currentContent = fgets($this->handle);
        $this->currentLine++;
    }

    /**
     * \Iterator::rewind()
     */
    public function rewind(): void
    {
        if (! $this->getHandle()) {
            return;
        }

        fclose($this->handle);
        $this->handle = fopen($this->filename, 'r');
        $this->currentContent = fgets($this->handle);
        $this->currentLine = 0;
    }

    /**
     * \Iterator::valid()
     */
    public function valid(): bool
    {
        if (! $this->getHandle()) {
            return false;
        }

        $valid = $this->currentContent !== false;

        if (! $valid) {
            fclose($this->handle);
        }

        return $valid;
    }

    /**
     * Returns the file handle.
     *
     * @return resource
     *   The file handle.
     */
    protected function getHandle()
    {
        if ($this->handle) {
            return $this->handle;
        }

        if (! file_exists($this->filename)) {
            return false;
        }

        return $this->handle = fopen($this->filename, 'r');
    }
}
