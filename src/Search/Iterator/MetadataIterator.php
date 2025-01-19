<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

use AdinanCenci\FileEditor\FileIterator;

/**
 * Iterator object to scrutinize the document line by line.
 */
class MetadataIterator extends FileIterator implements \Iterator
{
    /**
     * \Iterator::current()
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\MetadataWrapper
     *   Metadata wrapper.
     */
    public function current()
    {
        if (! $this->getHandle()) {
            return null;
        }

        return new MetadataWrapper($this->currentLine, rtrim($this->currentContent, "\n"));
    }
}
