<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class DeleteTest extends Base
{
    public function testDeleteSingleLine()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->deleteLine(2);

        $third = $file->getLine(2);
        $this->assertEquals('[ 3] Savage Circus', $third);
    }

    public function testDeleteMultipleLines()
    {
        $filename = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($filename);

        $file = new File($filename);
        $file->deleteLines([5, 8]);

        $lines = $file->getLines([5, 8]);
        $this->assertEquals([5 => '[ 6] Blind Guardian', 8 => '[10] '], $lines);
    }
}
