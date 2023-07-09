<?php
declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class DeleteTest extends Base
{
    public function testDeleteSingleLine() 
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $file->deleteLine(2);

        $third = $file->getLine(2);
        $this->assertEquals('Savage Circus', $third);
    }

    public function testDeleteMultipleLines() 
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $file->deleteLines([5, 8]);

        $lines = $file->getLines([5, 8]);
        $this->assertEquals([5 => 'Blind Guardian', 8 => ''], $lines);
    }
}
