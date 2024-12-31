<?php

declare(strict_types=1);

namespace AdinanCenci\FileEditor\Tests;

use AdinanCenci\FileEditor\File;
use AdinanCenci\FileEditor\Exception\DirectoryDoesNotExist;
use AdinanCenci\FileEditor\Exception\DirectoryIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileDoesNotExist;
use AdinanCenci\FileEditor\Exception\FileIsNotReadable;

final class ExceptionTest extends Base
{
    public function testWriteToNonExistentDirectory()
    {
        $file = new File('./tests/exception-tests/non-existent-directory/foo-bar.txt');
        $this->expectException(DirectoryDoesNotExist::class);
        $file->setLine(0, 'foo-bar');
    }

    public function testWriteToNonWritableDirectory()
    {
        $file = new File('./tests/exception-tests/non-writable-directory/foo-bar.txt');
        $this->expectException(DirectoryIsNotWritable::class);
        $file->setLine(0, 'foo-bar');
    }

    public function testWriteToNonWritableFile()
    {
        $file = new File('./tests/exception-tests/non-writable-file.txt');
        $this->expectException(FileIsNotWritable::class);
        $file->setLine(0, 'foo-bar');
    }

    public function testReadNonExistentFile()
    {
        $file = new File('./tests/exception-tests/non-existent-file.txt');
        $this->expectException(FileDoesNotExist::class);
        $entry = $file->getLine(0);
    }

    public function testReadFileWithoutReadingPermission()
    {
        $file = new File('./tests/exception-tests/non-readable-file.txt');
        $this->expectException(FileIsNotReadable::class);
        $entry = $file->getLine(0);
    }
}
