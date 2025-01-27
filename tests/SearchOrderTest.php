<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

class SearchOrderTest extends Base
{
    public function testorderByLength()
    {
        $file = new File('./tests/template-search.txt');

        $search = $file->search();
        $search->orderBy('length', 'ASC');
        $results = $search->find();

        $shortest = strlen(reset($results));
        $longest = strlen(end($results));

        $this->assertEquals(56, $shortest);
        $this->assertEquals(179, $longest);
    }

    public function testorderAlphabetically()
    {
        $file = new File('./tests/template-4.txt');

        $search = $file->search();
        $search->orderBy('content', 'DESC');
        $results = $search->find();

        $first = reset($results);
        $last = end($results);

        $this->assertEquals($first, 'Stratovarius');
        $this->assertEquals($last, 'Avantasia');
    }

    public function testOrderRandomly()
    {
        $file = new File('./tests/template-4.txt');

        $search1 = $file->search();
        $search1->orderRandomly();
        $results1 = $search1->find();

        $search2 = $file->search();
        $search2->orderRandomly();
        $results2 = $search2->find();

        $different = implode(',', $results1) != implode(',', $results2);

        $this->assertTrue($different);
    }

    public function testOrderRandomlyWithSeed()
    {
        $file = new File('./tests/template-4.txt');
        $seed = 'foo bar' . rand(0, 1000);

        $search1 = $file->search();
        $search1->orderRandomly($seed);
        $results1 = $search1->find();

        $search2 = $file->search();
        $search2->orderRandomly($seed);
        $results2 = $search2->find();

        $equal = implode(',', $results1) == implode(',', $results2);

        $this->assertTrue($equal);
    }
}
