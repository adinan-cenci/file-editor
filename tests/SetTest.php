<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class SetTest extends Base
{
    public function testSetSingleLine()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->setLine(9, 'Elvenking');

        $nine = $file->getLine(9);
        $this->assertEquals('Elvenking', $nine);
    }

    public function testSetMultipleLines()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->setLines([5 => 'Vis Mystica', 8 => 'Hammer King'], false);

        $lines = $file->getLines([5, 8]);
        $this->assertEquals([5 => 'Vis Mystica', 8 => 'Hammer King'], $lines);
    }

    public function testSetPastEndOfTheFile()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->setLine(25, 'Elvenking');

        $lastLine = $file->getLine(25);
        $this->assertEquals('Elvenking', $lastLine);
    }

    public function testCreateFileFromScratch()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $file = new File($filename);
        $file->setLine(0, 'Elvenking');

        $firstLine = $file->getLine(0);
        $this->assertEquals('Elvenking', $firstLine);
    }
}
