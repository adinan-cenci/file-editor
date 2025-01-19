<?php

namespace AdinanCenci\FileEditor;

class FileIterator implements \Iterator
{
    /**
     * @var string
     *   The absolute path to the file.
     */
    protected string $fileName = '';

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
     * @param string $fileName
     *   The absolute path to the file.
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function __get($var)
    {
        if ($var == 'currentLine') {
            return $this->currentLine;
        }
    }

    /**
     * \Iterator::current()
     */
    public function current()
    {
        if (! $this->getHandle()) {
            return null;
        }

        return $this->currentContent;
    }

    /**
     * \Iterator::key()
     */
    public function key()
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
        $this->handle = fopen($this->fileName, 'r');
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

        if (! file_exists($this->fileName)) {
            return false;
        }

        return $this->handle = fopen($this->fileName, 'r');
    }
}
