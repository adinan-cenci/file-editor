<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

use AdinanCenci\FileEditor\FileIterator;

/**
 * Iterator object to scrutinize the document line by line.
 */
class DataIterator extends FileIterator implements \Iterator
{
    /**
     * Constructor.
     *
     * @param string $fileName
     *   The absolute path to the file.
     * @param array $metadataGetters
     *   Array of callbacks to retrieve metadata.
     * @param array $metadataSetters
     *   Array of callbacks to set metadata.
     */
    public function __construct(
        protected string $fileName,
        protected array $metadataGetters,
        protected array $metadataSetters,
    ) {
    }

    /**
     * \Iterator::current()
     *
     * @return AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface
     *   Metadata wrapper.
     */
    public function current(): mixed
    {
        if (! $this->getHandle()) {
            return null;
        }

        $dataWrapper = new DataWrapper(rtrim($this->currentContent, "\n"));

        $compiledMetadata = $this->compileMetadata();
        $metadata = new Metadata($dataWrapper, $compiledMetadata, $this->metadataGetters);

        $dataWrapper->setMetadata($metadata);

        return $dataWrapper;
    }

    /**
     * Compiles metadata.
     *
     * @return array
     *   Compiled metadata.
     */
    protected function compileMetadata(): array
    {
        $metadata = [];
        foreach ($this->metadataSetters as $property => $callable) {
            $metadata[$property] = call_user_func($callable, $this);
        }
        return $metadata;
    }
}
