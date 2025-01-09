<?php

namespace AdinanCenci\FileEditor;

use AdinanCenci\FileEditor\Search\Search;

/**
 * @property string $fileName
 *   The filename.
 * @property FileIterator $lines
 *   Iterator object to read the file line by line.
 * @property int $lineCount
 *   The number of lines in the file.
 */
class File
{
    /**
     * @var string
     *   Absolute path to the file.
     */
    protected string $fileName;

    /**
     * @param string $fileName
     *   Absolute path to the file.
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Return read only properties.
     *
     * @param string $propertyName
     *   Property name.
     */
    public function __get(string $propertyName)
    {
        switch ($propertyName) {
            case 'lines':
                return $this->lines();
                break;
            case 'fileName':
            case 'filename':
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
     * Returns an object to iterate through the lines in the file.
     *
     * @return \Iterator
     *   The iterator object.
     */
    public function lines(): \Iterator
    {
        return new FileIterator($this->fileName);
    }

    /**
     * Instantiate a new search object.
     *
     * @param string $operator
     *   The operator to with wich avaliate the search conditions:
     *   "AND" or "OR".
     *
     * @return AdinanCenci\FileEditor\Search\Search
     *   The search object.
     */
    public function search(string $operator = 'AND'): Search
    {
        return new Search($this, $operator);
    }

    /**
     * Adds a new line to the file.
     *
     * Nothing is overwritten.
     *
     * @param string $content
     *   The content of the new line.
     * @param int $line
     *   The position within the file, if not provided, $content will be added
     *   to the end of the file.
     *
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function addLine(string $content, int $line = -1): void
    {
        $this->addLines([$line => $content], $line < 0);
    }

    /**
     * Adds multiple lines to the file.
     *
     * Nothing is overwritten.
     *
     * @param string[] $lines
     *   A numerical array: [ lineNumber => content ].
     * @param bool $toTheEndOfTheFile
     *   If true, places the lines to the end of the file.
     *   If false, the placement will reflect the array's keys.
     *
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function addLines(array $lines, bool $toTheEndOfTheFile = true): void
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
     * Sets the content of the specified line.
     *
     * It will ovewrite the line if already set.
     *
     * @param int $line
     *   The position within the file.
     * @param string $content
     *   The new content of the line.
     *
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function setLine(int $line, string $content): void
    {
        $this->setLines([$line => $content]);
    }

    /**
     * Sets the content of the specified lines.
     *
     * @param string[] $lines
     *   A numerical array: [ lineNumber => content ].
     *   Will ovewrite the lines if already set.
     *
     * @throws DirectoryDoesNotExist
     * @throws DirectoryIsNotWritable
     * @throws FileIsNotWritable
     */
    public function setLines(array $lines): void
    {
        $this->crud()
            ->set($lines)
            ->commit();
    }

    /**
     * Retrieves the content of the specified line.
     *
     * @param int $line
     *   The position within the file.
     *
     * @return string|null
     *   The contents of the line, null if there is nothing there.
     *
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function getLine(int $line): ?string
    {
        $contents = $this->getLines([$line]);
        return $contents ? reset($contents) : null;
    }

    /**
     * Returns the content of multiple lines at once.
     *
     * @param int[] $lines
     *   The positions within the file.
     *
     * @return (string|null)[]
     *   The contents of the specified lines.
     *
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function getLines(array $lines): array
    {
        return $this->crud()
            ->get($lines)
            ->commit()
            ->linesRetrieved;
    }

    /**
     * Deletes the specified line.
     *
     * @param int $line
     *   The position within the file.
     *
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function deleteLine(int $line): void
    {
        $this->deleteLines([$line]);
    }

    /**
     * Delete multiple lines at once.
     *
     * @param int[] $lines
     *   The positions within the file.
     *
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     */
    public function deleteLines(array $lines): void
    {
        $this->crud()
            ->delete($lines)
            ->commit();
    }

    /**
     * Returns random lines from the file.
     *
     * @param int $count
     *   How many lines to return.
     * @param int|null $from
     *   Limits the pool of lines available.
     * @param int|null $to
     *   Limits the pool of lines available.
     *
     * @return string[]
     *   The lines we retrieved.
     */
    public function getRandomLines(int $count, ?int $from = null, ?int $to = null): array
    {
        $lastIndex = $this->nameLastLine(true) - 1;

        if (is_null($from)) {
            $from = 0;
        }

        if (is_null($to)) {
            $to = $lastIndex;
        }

        if ($from > $to) {
            $a = $from;

            $from = $to;
            $to = $a;
        }

        if ($to > $lastIndex) {
            $to = $lastIndex;
        }

        if ($from >= $to) {
            // Aight, I give up.
            return [];
        }

        $count = $from > 0 && $to - $from < $count
            ? $to - $from + 1
            : $count;

        $lines = [];
        while (count($lines) < $count) {
            $lines[] = rand($from, $to);
            $lines = array_unique($lines);
        }

        return $this->getLines($lines);
    }

    /**
     * Returns an instance of the class used to edit the file.
     *
     * @return AdinanCenci\FileEditor\Crud
     */
    public function crud(): Crud
    {
        return new Crud($this->fileName);
    }

    /**
     * Counts how many lines the file has.
     *
     * @param int|null $lastNonEmptyLine
     *   Will return the last non-empty line in the file.
     *
     * @return int
     *   The number of lines in the file.
     */
    public function countLines(&$lastNonEmptyLine = null): int
    {
        return self::countLinesOnFile($this->fileName, $lastNonEmptyLine);
    }

    /**
     * Returns the last line of the file.
     *
     * @param bool $ignoreEmptyLines
     *   If true, the method will return the last non-empty line.
     *
     * @return int
     *   The last line.
     */
    public function nameLastLine(bool $ignoreEmptyLines = false): int
    {
        return self::getLastLine($this->fileName, $ignoreEmptyLines);
    }

    /**
     * Returns the last line of the specified file.
     *
     * @param string $fileName
     *   Absolute path to the file.
     * @param bool $ignoreEmptyLines
     *   If true, the method will return the last non-empty line.
     *
     * @return int
     *   The last line.
     */
    public static function getLastLine(string $fileName, bool $ignoreEmptyLines = false): int
    {
        $lastLine = self::countLinesOnFile($fileName, $lastNonEmptyLine);
        return $ignoreEmptyLines && $lastNonEmptyLine !== null
            ? $lastNonEmptyLine
            : $lastLine;
    }

    /**
     * Counts how many lines there is on a file.
     *
     * @param string $fileName
     *   Absolute path to the file.
     *
     * @param null|int $lastNonEmptyLine
     *   Will return the last line that is not empty.
     *
     * @return int
     *   The number of lines in the file.
     */
    public static function countLinesOnFile(string $fileName, &$lastNonEmptyLine = null): int
    {
        if (! file_exists($fileName)) {
            return 0;
        }

        $handle = fopen($fileName, 'r');
        $lineCount = 1;
        $lastNonEmptyLine = null;

        $n = 0;
        while (! feof($handle)) {
            $line = fgets($handle, 4096);
            $lineCount += substr_count($line, PHP_EOL);
            $strlen = strlen(trim($line, "\n"));
            if ($strlen > 0) {
                $lastNonEmptyLine = null;
            } elseif ($lastNonEmptyLine === null && $strlen == 0) {
                $lastNonEmptyLine = $n;
            }
            $n++;
        }

        fclose($handle);
        return $lineCount;
    }
}
