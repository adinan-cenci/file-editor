<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class CountLinesTest extends Base
{
    public function testCountNumberOfLinesInAFile()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename, './tests/template-2.txt');

        $file = new File($filename);
        $numberOfLines = $file->countLines();

        $this->assertEquals(25, $numberOfLines);
    }

    public function testNameLastLine()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename, './tests/template-2.txt');

        $file = new File($filename);
        $lastLine = $file->nameLastLine();

        $this->assertEquals(25, $lastLine);
    }

    public function testNameLastNonEmptyLine()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename, './tests/template-2.txt');

        $file = new File($filename);
        $lastLine = $file->nameLastLine(true);

        $this->assertEquals(16, $lastLine);


        $filename = 'tests/files/' . __FUNCTION__ . '2.txt';
        $this->resetTest($filename, './tests/template-3.txt');

        $file = new File($filename);
        $lastLine = $file->nameLastLine(false);

        $this->assertEquals(25, $lastLine);
    }
}
