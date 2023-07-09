<?php 
namespace AdinanCenci\FileEditor;

class FileIterator implements \Iterator 
{
    protected string $fileName   = '';
    protected $handle            = null;
    protected $currentContent    = null;
    protected int $currentLine   = 0;

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

    public function current() 
    {
        if (! $this->getHandle()) {
            return null;
        }

        return $this->currentContent;
    }

    public function key() 
    {
        return $this->currentLine;
    }

    public function next() : void
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

    public function rewind() : void
    {
        if (! $this->getHandle()) {
            return;
        }

        fclose($this->handle);
        $this->handle = fopen($this->fileName, 'r');
        $this->currentContent = fgets($this->handle);
        $this->currentLine = 0;
    }

    public function valid() : bool
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
