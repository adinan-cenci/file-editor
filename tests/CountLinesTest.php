<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class CountLinesTest extends Base
{
    public function testCountNumberOfLinesInAFile() 
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName, './tests/template-2.txt');

        $file = new File($fileName);
        $numberOfLines = $file->countLines();

        $this->assertEquals(25, $numberOfLines);
    }

    public function testNameLastLine() 
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName, './tests/template-2.txt');

        $file = new File($fileName);
        $lastLine = $file->nameLastLine();

        $this->assertEquals(25, $lastLine);
    }

    public function testNameLastNonEmptyLine() 
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName, './tests/template-2.txt');

        $file = new File($fileName);
        $lastLine = $file->nameLastLine(true);

        $this->assertEquals(16, $lastLine);


        $fileName = 'tests/files/' . __FUNCTION__ . '2.txt';
        $this->resetTest($fileName, './tests/template-3.txt');

        $file = new File($fileName);
        $lastLine = $file->nameLastLine(false);

        $this->assertEquals(25, $lastLine);
    }

}
