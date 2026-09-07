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
     * @param array $compiledMetadata
     *   Compiled metadata.
     * @param array $computedMetadata
     *   Callbacks to retrieve metadata.
     */
    public function __construct(
        protected $dataWrapper,
        protected array $compiledMetadata = [],
        protected array $computedMetadata = []
    ) {
    }

    public function __isset(string $data): bool
    {
        return !is_null($this->__get($data));
    }

    public function __get(string $data): mixed
    {
        if (isset($this->compiledMetadata[$data])) {
            return $this->compiledMetadata[$data];
        }

        return isset($this->computedMetadata[$data])
            ? call_user_func($this->computedMetadata[$data], $this->dataWrapper)
            : null;
    }
}
