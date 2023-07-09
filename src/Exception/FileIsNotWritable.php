<?php 
namespace AdinanCenci\FileEditor\Exception;

class FileIsNotWritable extends \Exception 
{
    public function __construct($fileName, $code = 0, Throwable $previous = null) {
        $message = 'File ' . $fileName . ' is not writable.';
        parent::__construct($message, $code, $previous);
    }
}
