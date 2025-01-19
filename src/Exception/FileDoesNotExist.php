<?php

namespace AdinanCenci\FileEditor\Exception;

class FileDoesNotExist extends \Exception
{
    public function __construct($fileName, $code = 0, Throwable $previous = null)
    {
        $message = 'File ' . $fileName . ' does not exist.';
        parent::__construct($message, $code, $previous);
    }
}
