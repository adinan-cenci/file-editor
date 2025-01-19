<?php

namespace AdinanCenci\FileEditor\Exception;

class DirectoryIsNotWritable extends \Exception
{
    public function __construct($directory, $code = 0, Throwable $previous = null)
    {
        $message = 'Directory ' . $directory . ' is not writable.';
        parent::__construct($message, $code, $previous);
    }
}
