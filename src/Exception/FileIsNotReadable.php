<?php 
namespace AdinanCenci\FileEditor\Exception;

class FileIsNotReadable extends \Exception 
{
    public function __construct($fileName, $code = 0, Throwable $previous = null) {
        $message = 'File ' . $fileName . ' is not readable.';
        parent::__construct($message, $code, $previous);
    }
}
