<?php

namespace AdinanCenci\FileEditor\Exception;

class FileIsNotWritable extends \Exception
{
    public function __construct($filename, $code = 0, Throwable $previous = null)
    {
        $message = 'File ' . $filename . ' is not writable.';
        parent::__construct($message, $code, $previous);
    }
}
