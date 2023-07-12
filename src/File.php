<?php 
namespace AdinanCenci\FileEditor;

/**
 * @property string $fileName
 * @property FileIterator $lines Iterator object to read the file line by line.
 * @property int $lineCount The number of lines in the file.
 */
class File 
{
    protected string $fileName;

    public function __construct(string $fileName) 
    {
        $this->fileName = $fileName;
    }

    public function __get(string $propertyName) 
    {
        switch ($propertyName) {
            case 'lines':
                return new FileIterator($this->fileName);
                break;
            case 'fileName':
                return $this->fileName;
                break;
            case 'lineCount':
                return $this->countLines();
                break;
        }

        \trigger_error('Trying to retrieve unknown property ' . $propertyName, \E_USER_ERROR);
        return null;
    }

    /**
     * If $line is not specified, $content will be added at the end of the file.
     * 
     * @param string $content
     * @param int $line
     * 
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function addLine(string $content, int $line = -1) : void
    {
        $this->addLines([$line => $content], $line < 0);
    }

    /**
     * @param string[] $lines A numerical array: [ lineNumber => content ].
     * @param bool $toTheEndOfTheFile If true, places the lines to the end of the file.
     * If false, the placement will reflect the array's keys.
     * 
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function addLines(array $lines, bool $toTheEndOfTheFile = true) : void
    {
        if ($toTheEndOfTheFile) {
            $lastLine = $this->nameLastLine(true);
            $keys = range($lastLine, ($lastLine + count($lines)) - 1);
            $lines = array_combine($keys, array_values($lines));
        }

        $this->crud()
            ->add($lines)
            ->commit();
    }

    /**
     * Will ovewrite the line if already set, unlike ::addLine()
     * 
     * @param int $line
     * @param string $content 
     * 
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function setLine(int $line, string $content) : void
    {
        $this->setLines([$line => $content]);
    }

    /**
     * Will ovewrite the lines if already set, unlike ::addLines()
     * @param string[] $lines A numerical array: [ lineNumber => content ].
     * 
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function setLines(array $lines) : void
    {
        $this->crud()
            ->set($lines)
            ->commit();
    }

    /**
     * @param int $line
     * @return string|null
     * 
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function getLine(int $line) : ?string
    {
        $contents = $this->getLines([$line]);
        return $contents ? reset($contents) : null;
    }

    /**
     * @param int[] $lines
     * @return (string|null)[]
     * 
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function getLines(array $lines) : array
    {
        return $this->crud()
            ->get($lines)
            ->commit()
            ->linesRetrieved;
    }

    /**
     * @param int $line
     * 
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function deleteLine(int $line) : void
    {
        $this->deleteLines([$line]);
    }

    /**
     * @param int[] $lines
     * 
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function deleteLines(array $lines) : void
    {
        $this->crud()
            ->delete($lines)
            ->commit();
    }

    /**
     * Returns an instance of the class used to edit the file.
     * 
     * @return Crud
     */
    public function crud() : Crud
    {
        return new Crud($this->fileName);
    }

    /**
     * @param bool $emptyLastLine Will turn true if the last line of the file is empty.
     * @return int The number of lines in the file.
     */
    public function countLines(&$emptyLastLine = false) : int
    {
        return self::countLinesOnFile($this->fileName, $emptyLastLine);
    }

    /**
     * @param bool $ignoreEmptyLines If true, the method will return the last non empty line.
     * @return int
     */
    public function nameLastLine(bool $ignoreEmptyLines = false) : int
    {
        return self::getLastLine($this->fileName, $ignoreEmptyLines);
    }

    /**
     * @param string $fileName
     * @param bool $ignoreEmptyLines If true, the method will return the last non empty line.
     * @return int
     */
    public static function getLastLine(string $fileName, bool $ignoreEmptyLines = false) : int
    {
        $lastLine = self::countLinesOnFile($fileName, $lastNonEmptyLine);
        return $ignoreEmptyLines && $lastNonEmptyLine !== null ? $lastNonEmptyLine : $lastLine;
    }

    /**
     * @param string $fileName
     * @param bool $lastNonEmptyLine Will return the last line that is not empty.
     * @return int The actual number of lines in the file.
     */
    public static function countLinesOnFile(string $fileName, &$lastNonEmptyLine = null) : int
    {
        if (! file_exists($fileName)) {
            return 0;
        }

        $handle = fopen($fileName, 'r');
        $lineCount = 1;
        $lastNonEmptyLine = null;

        $n = 0;
        while(! feof($handle)){
            $line = fgets($handle, 4096);
            $lineCount += substr_count($line, PHP_EOL);
            $strlen = strlen(trim($line, "\n"));
            if ($strlen > 0) {
                $lastNonEmptyLine = null;
            } else if ($lastNonEmptyLine === null && $strlen == 0) {
                $lastNonEmptyLine = $n;
            }
            $n++;
        }

        fclose($handle);
        return $lineCount;
    }
}
