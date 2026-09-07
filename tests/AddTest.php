<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class AddTest extends Base
{
    public function testAddSingleLine()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->addLine('Elvenking');

        $lastLine = $file->getLine(16);
        $this->assertEquals('Elvenking', $lastLine);
    }

    public function testAddMultipleLinesToTheEndOfTheFile()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->addLines(['[16] Vis Mystica', '[17] Hammer King'], true);

        $lines = $file->getLines([16, 17]);
        $this->assertEquals([16 => '[16] Vis Mystica', 17 => '[17] Hammer King'], $lines);
    }

    public function testAddMultipleLinesToTheEndOfTheFileWithBlankLinesAtTheEnd()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename, './tests/template-2.txt');

        $file = new File($filename);
        $file->addLines(['[16] Vis Mystica', '[17] Hammer King'], true);

        $lines = $file->getLines([16, 17]);
        $this->assertEquals([16 => '[16] Vis Mystica', 17 => '[17] Hammer King'], $lines);
    }

    public function testAddMultipleLines()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->addLines([5 => 'Vis Mystica', 8 => 'Hammer King'], false);

        $lines = $file->getLines([5, 8]);
        $this->assertEquals([5 => 'Vis Mystica', 8 => 'Hammer King'], $lines);
    }

    public function testAddPastEndOfTheFile()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->addLine('Elvenking', 25);

        $lastLine = $file->getLine(25);
        $this->assertEquals('Elvenking', $lastLine);
    }

    public function testAddLinesWithGaps()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename, '');

        $file = new File($filename);

        $file->addLines([
            0 => 'Dreamtale',
            10 => 'Unleash The Archers'
        ], false);

        $lines = $file->getLines([0, 10]);
        $this->assertEquals([
            0 => 'Dreamtale',
            10 => 'Unleash The Archers'
        ], $lines);
    }

    public function testCreateFromScratch()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        if (file_exists($filename)) {
            unlink($filename);
        }

        $file = new File($filename);

        $file->addLines(['first', 'second', 'third', '', 'fifth']);

        $this->assertTrue(true);
    }
}
