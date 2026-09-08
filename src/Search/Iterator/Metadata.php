<?php

namespace AdinanCenci\FileEditor\Search\Iterator;

/**
 * {@inheritdoc}
 */
class Metadata implements MetadataInterface
{
    /**
     * Constructor.
     *
     * @param AdinanCenci\FileEditor\Search\Iterator\DataWrapperInterface $dataWrapper
     *   Data wrapper object.
     * @param array $eagerMetadata
     *   Eagerly compiled metadata.
     * @param array $metadataLazyGetters
     *   Lazyly generated metadata.
     */
    public function __construct(
        protected $dataWrapper,
        protected array $eagerMetadata = [],
        protected array $metadataLazyGetters = []
    ) {
    }

    public function __isset(string $data): bool
    {
        return !is_null($this->__get($data));
    }

    public function __get(string $data): mixed
    {
        if (array_key_exists($data, $this->eagerMetadata)) {
            return $this->eagerMetadata[$data];
        }

        return isset($this->metadataLazyGetters[$data])
            ? call_user_func($this->metadataLazyGetters[$data], $this->dataWrapper)
            : null;
    }
}
