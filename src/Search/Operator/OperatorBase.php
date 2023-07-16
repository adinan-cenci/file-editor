<?php 
namespace AdinanCenci\FileEditor\Search\Operator;

abstract class OperatorBase implements OperatorInterface 
{
    protected $actualValue;
    protected $valueToCompare;

    public function __construct($actualValue, $valueToCompare) 
    {
        $this->actualValue = self::normalize($actualValue);
        $this->valueToCompare = self::normalize($valueToCompare);
        $this->validateValueToCompare();
    }

    /**
     * @inheritDoc
     */
    public function matches() : bool
    {

    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validateValueToCompare() : void
    {

    }

    protected function isScalar($data) : bool
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

        return trim( strtolower( (string) $data) );
    }

    protected function normalizeArray(array $data) : array
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

    protected function invalidDataError(string $operatorName, string $expected, string $actual) : string
    {
        return 'Invalid data given to ' . $operatorName . ' operator, expected ' . 
        $expected . ( $actual ? (', ' . $actual . ' given.') : '' );
    }

    protected static function isNumericalArray(array $array) : bool
    {
        $keys = array_keys($array);
        return $keys === array_keys($keys);
    }
}
