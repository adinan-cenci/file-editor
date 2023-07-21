<?php 
namespace AdinanCenci\FileEditor;

/**
 * It will return ScrutinyLine objects, useful for search operations.
 */
class ScrutinyIterator extends FileIterator implements \Iterator 
{
    public function current() 
    {
        if (! $this->getHandle()) {
            return null;
        }

        return new ScrutinyLine($this->currentLine, rtrim($this->currentContent, "\n"));
    }
}
