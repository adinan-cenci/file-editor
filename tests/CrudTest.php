<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;

final class CrudTest extends Base
{
    public function testMultipleOperationsAtOnce()
    {
        $fileName = 'tests/files/' . __FUNCTION__ . '.txt';
        $this->resetTest($fileName);

        $file = new File($fileName);
        $crud = $file->crud();
        $crud
            ->add([0 => 'Dark Moor']) // Dark Moor added to the beginning
            ->set([3 => 'Beast in Black']) // [ 3] Savage Circus leaves, Beast in black in its place
            ->delete([9]) // '[ 9] Nightwish' is removed, '[10] ' is still the 10th because of Dark moor though
            ->get([0])
            ->commit();

        $first = $file->getLine(0);
        $this->assertEquals('Dark Moor', $first);

        $fourth = $file->getLine(3);
        $this->assertEquals('Beast in Black', $fourth);

        $tenth = $file->getLine(9);
        $this->assertEquals('[ 8] Gamma ray', $tenth);

        $retrieved = $crud->linesRetrieved;
        $this->assertEquals('[ 0] Avantasia', $retrieved[0]);
    }
}
