<?php

namespace AdinanCenci\FileEditor\Search\Operation;

abstract class OperationFabric
{
    /**
     * Instantiates a new operation.
     *
     * @param mixed $leftOperand
     *   The first operand.
     * @param mixed $rightOperand
     *   The second operand.
     * @param string $operatorId
     *   String identifying the operation.
     *
     * @return AdinanCenci\FileEditor\Search\Operation\OperationInterface
     *   The operator.
     */
    public static function newOperation(
        mixed $actualValue,
        mixed $valueToCompare,
        string $operatorId
    ): OperationInterface {
        $class = self::getOperationClass($operatorId, $negate);
        $instance = new $class($actualValue, $valueToCompare, $negate);

        return $instance;
    }

    /**
     * Checks if $operatorId is valid.
     *
     * If it is associated to an operator.
     *
     * @return bool
     *   The $operatorId validity.
     */
    public static function isValidOperatorId(string $operatorId)
    {
        return (bool) self::getOperationClass($operatorId);
    }

    /**
     * Gets the operator class associated to $operatorId.
     *
     * @param string $operatorId
     *   A string representing an operator.
     * @param bool $negating
     *   Turns true if $operatorId is negating the operation.
     *
     * @return string|null
     *   The class for the operator.
     */
    protected static function getOperationClass(string $operatorId, &$negating = false): ?string
    {
        $negating = substr_count($operatorId, '!') ||
          substr_count($operatorId, 'NOT') ||
          $operatorId == 'UNLIKE';

        switch ($operatorId) {
            case '=':
            case '!=':
                return EqualOperation::class;
                break;
            case 'IN':
            case 'NOT IN':
            case '!IN':
                return IncludeOperation::class;
                break;
            case 'LIKE':
            case 'NOT LIKE':
            case '!LIKE':
                return LikeOperation::class;
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
            case '!BETWEEN':
                return BetweenOperation::class;
                break;
            case 'IS NULL':
            case 'NOT NULL':
            case '!NULL':
                return IsNullOperation::class;
                break;
            case '<':
                return LessThanOperation::class;
                break;
            case '>':
                return GreaterThanOperation::class;
                break;
            case '<=':
                return LessThanOrEqualToOperation::class;
                break;
            case '>=':
                return GreaterThanOrEqualToOperation::class;
                break;
            case 'REGEX':
                return RegexOperation::class;
                break;
        }

        return null;
    }
}
