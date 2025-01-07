<?php
namespace AdinanCenci\FileEditor\Search\Condition;

use AdinanCenci\FileEditor\Search\Iterator\MetadataWrapperInterface;
use AdinanCenci\FileEditor\Search\Operation\OperatorInterface;
use AdinanCenci\FileEditor\Search\Operation\Equals;
use AdinanCenci\FileEditor\Search\Operation\Includes;
use AdinanCenci\FileEditor\Search\Operation\OperationFabric;

class Condition implements ConditionInterface
{
    /**
     * @var string[]
     *   A path to extract the actual value during evaluation.
     */
    protected array $propertyPath;

    /**
     * @var mixed
     *   The value for comparison.
     */
    protected mixed $valueToCompare;

    /**
     * @var string
     *   The operator.
     */
    protected string $operator;

    /**
     * @param array|string[] $propertyPath
     *   A path to extract the actual value during evaluation.
     * @param mixed $valueToCompare
     *   The value for comparison.
     * @param string $operator
     *   The operator.
     */
    public function __construct(mixed $propertyPath, mixed $valueToCompare, string $operator = '=')
    {
        $this->propertyPath   = (array) $propertyPath;
        $this->valueToCompare = $valueToCompare;
        $this->operator       = $operator;

        if (!OperationFabric::isValidOperatorId($operator)) {
            throw new \InvalidArgumentException('Unrecognized operator ' . $operator);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function evaluate(MetadataWrapperInterface $data): bool
    {
        $actualValue = $data->getValue($this->propertyPath);
        $operator    = OperationFabric::newOperation($actualValue, $this->valueToCompare, $this->operator);
        $result      = $operator->matches();

        return $result;
    }

    /**
     * Retrieves the value we want from $data, given the path.
     *
     * @param array|\stdClass $data
     *   Data to extract the value from.
     * @param string[] $propertyPath
     *   A path to extract the value from $data.
     *
     * @return string|int|float|bool|null|array|\stdClass
     *   Tha value extracted from $data.
     */
    protected function getValue($data, array $propertyPath)
    {
        foreach ($propertyPath as $part) {
            if (is_object($data) && isset($data->{$part})) {
                $data = $data->{$part};
            } elseif (is_array($data) && isset($data[$part])) {
                $data = $data[$part];
            } else {
                return null;
            }
        }

        return $data;
    }
}
