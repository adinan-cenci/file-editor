<?php 
namespace AdinanCenci\FileEditor\Search\Condition;

use AdinanCenci\FileEditor\Search\Operator\OperatorInterface;
use AdinanCenci\FileEditor\Search\Operator\Equals;
use AdinanCenci\FileEditor\Search\Operator\Includes;

class Condition implements ConditionInterface 
{
    protected array $property;
    protected $valueToCompare;
    protected string $operatorClass;
    protected bool $negate = false;

    const EQUALS_OPERATOR                   = 'AdinanCenci\FileEditor\Search\Operator\EqualOperator';
    const INCLUDES_OPERATOR                 = 'AdinanCenci\FileEditor\Search\Operator\IncludeOperator';
    const LIKES_OPERATOR                    = 'AdinanCenci\FileEditor\Search\Operator\LikeOperator';
    const BETWEEM_OPERATOR                  = 'AdinanCenci\FileEditor\Search\Operator\BetweenOperator';
    const IS_NULL_OPERATOR                  = 'AdinanCenci\FileEditor\Search\Operator\IsNullOperator';
    const LESS_THAN_OPERATOR                = 'AdinanCenci\FileEditor\Search\Operator\LessThanOperator';
    const GREATER_THAN_OPERATOR             = 'AdinanCenci\FileEditor\Search\Operator\GreaterThanOperator';
    const LESS_THAN_OR_EQUAL_TO             = 'AdinanCenci\FileEditor\Search\Operator\LessThanOrEqualToOperator';
    const GREATER_THAN_OR_EQUAL_TO_OPERATOR = 'AdinanCenci\FileEditor\Search\Operator\GreaterThanOrEqualToOperator';
    const REGEX_OPERATOR                    = 'AdinanCenci\FileEditor\Search\Operator\RegexOperator';

    /**
     * @param array|string[] $property Either a simle string or an array of strings to reache nested properties.
     * @param mixed $valueToCompare
     * @param string $operatorId
     */
    public function __construct($property, $valueToCompare, string $operatorId = '=') 
    {
        $this->property       = (array) $property;
        $this->valueToCompare = $valueToCompare;
        $class                = $this->getOperatorClass($operatorId, $negation);

        if (is_null($class)) {
            throw new \InvalidArgumentException('Unrecognized operator ' . $operatorId);
        }

        $this->operatorClass = $class;
        $this->negation      = $negation;
    }

    /**
     * @inheritDoc
     */
    public function evaluate($data) : bool
    {
        $actualValue = $this->getValue($data, $this->property);
        $operator    = new $this->operatorClass($actualValue, $this->valueToCompare);
        $result      = $operator->matches();

        return $this->negation
            ? !$result
            : $result;
    }

    /**
     * Retrieves from $data the value we are going to submit to the operator.
     * 
     * @param array|\stdClass $data
     * @param array $property
     * @return string|int|float|bool|null|array|\stdClass
     */
    protected function getValue($data, array $property) 
    {
        foreach ($property as $part) {
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

    /**
     * @param string $operatorId A string representing an operation.
     * @param bool $negation Turns true if $operatorId is negating the operation.
     * @return string|null The class name for the operation.
     */
    protected function getOperatorClass(string $operatorId, &$negation = false) : ?string
    {
        $negation = substr_count($operatorId, '!') || 
          substr_count($operatorId, 'NOT') || 
          $operatorId == 'UNLIKE';

        switch ($operatorId) {
            case '=' :
            case '!=' :
                return self::EQUALS_OPERATOR;
                break;
            case 'IN' :
            case 'NOT IN' :
            case '!IN' :
                return self::INCLUDES_OPERATOR;
                break;
            case 'LIKE' :
            case 'NOT LIKE' :
            case '!LIKE' :
                return self::LIKES_OPERATOR;
                break;
            case 'BETWEEN' :
            case 'NOT BETWEEN' :
            case '!BETWEEN' :
                return self::BETWEEM_OPERATOR;
                break;
            case 'IS NULL':
            case 'NOT NULL':
            case '!NULL':
                return self::IS_NULL_OPERATOR;
                break;
            case '<':
                return self::LESS_THAN_OPERATOR;
                break;
            case '>':
                return self::GREATER_THAN_OPERATOR;
                break;
            case '<=':
                return self::LESS_THAN_OR_EQUAL_TO;
                break;
            case '>=':
                return self::GREATER_THAN_OR_EQUAL_TO_OPERATOR;
                break;
            case 'REGEX':
                return self::REGEX_OPERATOR;
                break;
        }

        return null;
    }
}
