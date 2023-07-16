<?php 
namespace AdinanCenci\FileEditor;

/**
 * @property string $content The actual line.
 * @property int $lineNumber The positon of the line in the file.
 * @property int $length The character length of the line.
 */
class ScrutinyLine 
{
    protected int $lineNumber;
    protected string $content;

    public function __construct(int $lineNumber, string $content) 
    {
        $this->lineNumber = $lineNumber;
        $this->content = $content;
    }

    public function __toString() 
    {
        return $this->content;
    }

    public function __get(string $propertyName) 
    {
        switch ($propertyName) {
            case 'lineNumber':
                return $this->lineNumber;
                break;
            case 'content':
                return $this->content;
                break;
            case 'length':
                return strlen($this->content);
                break;
        }

        \trigger_error('Trying to retrieve unknown property ' . $propertyName, \E_USER_ERROR);
        return null;
    }

    public function __isset(string $propertyName) 
    {
        return in_array($propertyName, ['lineNumber', 'content', 'length']);
    }
}
