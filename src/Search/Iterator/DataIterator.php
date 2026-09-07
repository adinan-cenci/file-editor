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
     * @param string $filename
     *   The absolute path to the file.
     * @param array $metadataEagerGetters
     *   Array of callbacks to extract metadata for search results.
     * @param array $metadataLazyGetters
     *   Array of callbacks to extract metadata for search results.
     */
    public function __construct(
        protected string $filename,
        protected array $metadataEagerGetters = [],
        protected array $metadataLazyGetters = [],
    ) {
        parent::__construct($filename);
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

        $eagerMetadata = $this->compileEagerMetadata();
        $metadata = new Metadata($dataWrapper, $eagerMetadata, $this->metadataLazyGetters);

        $dataWrapper->setMetadata($metadata);

        return $dataWrapper;
    }

    /**
     * Compiles eager metadata.
     *
     * @return array
     *   Compiled metadata.
     */
    protected function compileEagerMetadata(): array
    {
        $metadata = [];
        foreach ($this->metadataEagerGetters as $property => $callable) {
            $metadata[$property] = call_user_func($callable, $this);
        }
        return $metadata;
    }
}
