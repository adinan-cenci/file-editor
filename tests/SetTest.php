<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class SetTest extends Base
{
    public function testSetSingleLine()
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $file->setLine(9, 'Elvenking');

        $nine = $file->getLine(9);
        $this->assertEquals('Elvenking', $nine);
    }

    public function testSetMultipleLines()
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $file->setLines([5 => 'Vis Mystica', 8 => 'Hammer King'], false);

        $lines = $file->getLines([5, 8]);
        $this->assertEquals([5 => 'Vis Mystica', 8 => 'Hammer King'], $lines);
    }

    public function testSetPastEndOfTheFile()
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $file->setLine(25, 'Elvenking');

        $lastLine = $file->getLine(25);
        $this->assertEquals('Elvenking', $lastLine);
    }

    public function testCreateFileFromScratch()
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $file = new File($fileName);
        $file->setLine(0, 'Elvenking');

        $firstLine = $file->getLine(0);
        $this->assertEquals('Elvenking', $firstLine);
    }
}
