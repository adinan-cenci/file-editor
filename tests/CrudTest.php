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
            ->add([0 => 'Dark Moor'])
            ->set([3 => 'Beast in Black'])
            ->delete([9])
            ->get([0])
            ->commit();

        $first = $file->getLine(0);
        $this->assertEquals('Dark Moor', $first);

        $fifth = $file->getLine(4);
        $this->assertEquals('Beast in Black', $fifth);

        $tenth = $file->getLine(9);
        $this->assertEquals('Gamma ray', $tenth);

        $retrieved = $crud->linesRetrieved;
        $this->assertEquals('Avantasia', $retrieved[0]);
    }
}
