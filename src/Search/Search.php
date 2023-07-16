<?php 
namespace AdinanCenci\FileEditor\Search;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Search\Condition\ConditionGroupInterface;
use AdinanCenci\FileEditor\Search\Condition\AndConditionGroup;
use AdinanCenci\FileEditor\Search\Condition\OrConditionGroup;

class Search implements ConditionGroupInterface 
{
    protected File $file;
    protected ConditionGroupInterface $mainConditionGroup;

    public function __construct(File $file, string $operator = 'AND') 
    {
        $this->file = $file;
        $this->mainConditionGroup = $operator == 'OR'
            ? new OrConditionGroup()
            : new AndConditionGroup();
    }

    /**
     * It will iterate through the file and return the objects that match
     * the specified criteria.
     * @return array
     */
    public function find() : array
    {
        $results = [];
        foreach ($this->file->scrutinyLines as $line => $object) {
            if ($object && $this->evaluate($object)) {
                $results[ $line ] = $object->content;
            }
        }
        return $results;
    }

    /**
     * @inheritDoc
     */
    public function evaluate($data) : bool
    {
        return $this->mainConditionGroup->evaluate($data);
    }

    /**
     * @inheritDoc
     */
    public function condition($property, $valueToCompare, string $operatorId = '=') : self
    {
        $this->mainConditionGroup->condition($property, $valueToCompare, $operatorId);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function andConditionGroup() : AndConditionGroup
    {
        return $this->mainConditionGroup->andConditionGroup();
    }

    /**
     * @inheritDoc
     */
    public function orConditionGroup() : OrConditionGroup
    {
        return $this->mainConditionGroup->orConditionGroup();
    }
}
