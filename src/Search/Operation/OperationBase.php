<?php

namespace AdinanCenci\FileEditor\Search\Operation;

abstract class OperationBase implements OperationInterface
{
    /**
     * @var mixed
     *   The first operand.
     */
    protected $leftOperand;

    /**
     * @var mixed
     *   The second operand.
     */
    protected $rightOperand;

    /**
     * @var bool
     *   Wether to negate the operation's result or not.
     */
    protected $negate;

    /**
     * {@inheritDoc}
     */
    public function __construct(mixed $leftOperand, mixed $rightOperand, bool $negate = false)
    {
        $this->leftOperand    = self::normalize($leftOperand);
        $this->rightOperand   = self::normalize($rightOperand);
        $this->negate         = $negate;
        $this->validateValueToCompare();
    }

    /**
     * {@inheritDoc}
     */
    public function matches(): bool
    {
        $result = $this->compare();

        return $this->negate
            ? !$result
            : $result;
    }

    protected function compare(): bool
    {
        return true;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateValueToCompare(): void
    {

    }

    protected function isScalar($data): bool
    {
        if (is_bool($data)) {
            return true;
        }

        if (is_null($data)) {
            return true;
        }

        return is_scalar($data);
    }

    protected function normalize($data)
    {
        $data = is_object($data)
            ? (array) $data
            : $data;

        return is_array($data)
            ? $this->normalizeArray($data)
            : $this->normalizeScalar($data);
    }

    protected function normalizeScalar($data)
    {
        if (is_bool($data)) {
            return $data;
        }

        return trim(strtolower((string) $data));
    }

    protected function normalizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if ($this->isScalar($value)) {
                $data[$key] = $this->normalizeScalar($value);
            }
        }

        if ($this->isNumericalArray($data)) {
            sort($data);
        }

        return $data;
    }

    protected function invalidDataError(string $operatorName, string $expected, string $actual): string
    {
        return 'Invalid data given to ' . $operatorName . ' operator, expected ' .
        $expected . ( $actual ? (', ' . $actual . ' given.') : '' );
    }

    protected static function isNumericalArray(array $array): bool
    {
        $keys = array_keys($array);
        return $keys === array_keys($keys);
    }
}
