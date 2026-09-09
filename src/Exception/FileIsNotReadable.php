<?php

namespace AdinanCenci\FileEditor\Exception;

class FileIsNotReadable extends \Exception
{
    public function __construct($filename, $code = 0, Throwable $previous = null)
    {
        $message = 'File ' . $filename . ' is not readable.';
        parent::__construct($message, $code, $previous);
    }
}
