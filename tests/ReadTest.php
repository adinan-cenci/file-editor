<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class ReadTest extends Base
{
    public function testGetSingleLine()
    {
        $file = new File('tests/template.txt');
        $thirLine = $file->getLine(2);

        $this->assertEquals('[ 2] Halloween', $thirLine);
    }

    public function testGetNonExistentLine()
    {
        $file = new File('tests/template.txt');
        $thirLine = $file->getLine(50);

        $this->assertEquals(null, $thirLine);
    }

    public function testGetMultipleLines()
    {
        $file = new File('tests/template.txt');
        $lines = $file->getLines([0, 2, 4]);

        $this->assertEquals([
            0 => '[ 0] Avantasia',
            2 => '[ 2] Halloween',
            4 => '[ 4] Stratovarius'
        ], $lines);
    }

    public function testGetRandomLines()
    {
        $file = new File('tests/template.txt');
        $lines = $file->getRandomLines(5);
        $this->assertEquals(5, count($lines));

        $lines = $file->getRandomLines(2, 0, 1);
        $this->assertEquals(2, count($lines));

        $lines = $file->getRandomLines(2, 2, 15);
        $this->assertEquals(2, count($lines));

        $lines = $file->getRandomLines(10, 14, 15);
        $this->assertEquals(2, count($lines));
    }
}
