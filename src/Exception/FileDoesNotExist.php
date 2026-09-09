<?php

namespace AdinanCenci\FileEditor\Exception;

class FileDoesNotExist extends \Exception
{
    public function __construct($filename, $code = 0, Throwable $previous = null)
    {
        $message = 'File ' . $filename . ' does not exist.';
        parent::__construct($message, $code, $previous);
    }
}
