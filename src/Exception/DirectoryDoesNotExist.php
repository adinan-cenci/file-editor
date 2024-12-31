<?php

namespace AdinanCenci\FileEditor\Exception;

class DirectoryDoesNotExist extends \Exception
{
    public function __construct($dirName, $code = 0, Throwable $previous = null)
    {
        $message = 'Directory ' . $dirName . ' does not exist.';
        parent::__construct($message, $code, $previous);
    }
}
