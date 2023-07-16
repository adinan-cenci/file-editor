<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

class SearchTest extends Base
{
    public function testSearchEqualsOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();
        $search->condition('lineNumber', 3, '=');
        $results = $search->find();
        $this->assertEquals("And which of the gods was it that set them on to quarrel? ", $results[3]);

        $search = $file->search();
        $search->condition('content', "It was the son of Jove and Leto; for he was angry with the king and sent a pestilence upon the host to plague the people, ", '=');
        $results = $search->find();

        $keys = array_keys($results);
        $lineN = reset($keys);

        $this->assertEquals(4, $lineN);
    }

    public function testSearchGreaterThanOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('lineNumber', 30, '>');

        $results = $search->find();

        $this->assertEquals(2, count($results));
    }

    
    public function testSearchGreaterOrEqualToOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('lineNumber', 30, '>=');

        $results = $search->find();

        $this->assertEquals(3, count($results));
    }

    public function testSearchBetweenOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('lineNumber', [-1, 3], 'BETWEEN');

        $results = $search->find();

        $this->assertEquals([0, 1, 2], array_keys($results));
    }

    public function testSearchIncludeOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('lineNumber', [0, 2, 4, 6], 'IN');

        $results = $search->find();

        $this->assertEquals([0, 2, 4, 6], array_keys($results));
    }

    public function testSearchLikeOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('content', 'Achaeans', 'LIKE');

        $results = $search->find(); 

        $this->assertEquals([0, 6, 7, 9, 11, 24, 31], array_keys($results));
    }

    public function testSearchRegexOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search->condition('content', '#O god#', 'REGEX');

        $results = $search->find(); 

        $this->assertEquals([0, 17], array_keys($results));
    }

    public function testSearchInvalidOperator() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $this->expectException('InvalidArgumentException');
        $search->condition('content', 'test', 'foo-bar');
    }

    //--------------------------

    public function testAndSearchWithTwoConditions() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search();

        $search
            ->condition('content', 'daughter', 'LIKE')
            ->condition('content', 'son', 'LIKE');

        $results = $search->find();
        $keys = array_keys($results);

        $this->assertEquals(10, $keys[0]);
    }

    public function testOrSearchWithTwoConditions() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search('OR');

        $search
            ->condition('content', 'daughter', 'LIKE')
            ->condition('content', 'son', 'LIKE');

        $results = $search->find();

        $this->assertEquals([0, 2, 4, 5, 6, 8, 9, 10, 26, 30], array_keys($results));
    }

    public function testOrSearchMultiLevelConditions() 
    {
        $file = new File('./tests/template-search.txt');
        $search = $file->search('OR');

        $search
            ->andConditionGroup()
                ->condition('lineNumber', 0, '>')
                ->condition('lineNumber', 2, '<');

        $search
            ->andConditionGroup()
                ->condition('lineNumber', 6, '>')
                ->condition('lineNumber', 8, '<');

        $results = $search->find();

        $this->assertEquals([1, 7], array_keys($results));
    }
}
