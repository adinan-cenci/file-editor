<?php 
namespace AdinanCenci\FileEditor;

use AdinanCenci\FileEditor\Exception\DirectoryDoesNotExist;
use AdinanCenci\FileEditor\Exception\DirectoryIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileDoesNotExist;
use AdinanCenci\FileEditor\Exception\FileIsNotReadable;

class Crud 
{
    protected string $fileName = '';
    protected FileIterator $iterator;

    //-----------------------------

    protected string $tempFileName = '';
    protected $tempFileResource = null;
    protected int $tempFileCurrentLine = 0;

    //-----------------------------

    /** @var int $finalLine Property to controll the loop. */
    protected int $finalLine = 0;

    /** @var int[] $linesToGet */
    protected array $linesToGet = [];

    /** @var int[] $linesToDelete */
    protected array $linesToDelete = [];

    /** @var string[] $linesToAdd */
    protected array $linesToAdd = [];

    /** @var int $lastLineToBeAdd */
    protected $lastLineToBeAdd = 0;

    /** @var (string|null)[] $linesRetrieved */
    protected array $linesRetrieved = [];

    /** @var int $lastLineToBeRetrieved */
    protected $lastLineToBeRetrieved = 0;

    //-----------------------------

    public function __construct(string $fileName) 
    {
        $this->fileName = $fileName;
    }

    public function __get(string $propertyName) 
    {
        if ($propertyName == 'linesRetrieved') {
            return $this->linesRetrieved;
        }

        \trigger_error('Trying to retrieve unknown property ' . $propertyName, \E_USER_ERROR);
    }

    /**
     * @param int[] $lines An array of integers.
     */
    public function get(array $lines) : self
    {
        $this->linesToGet = $lines;
        return $this;
    }

    /**
     * @param int[] $lines An array of integers.
     */
    public function delete(array $lines) : self
    {
        $this->linesToDelete = array_merge($this->linesToDelete, $lines);
        return $this;
    }

    public function add(array $lines) : self
    {
        $this->linesToAdd += $lines;
        return $this;
    }

    public function set(array $lines) : self
    {
        if (file_exists($this->fileName)) {
            $this->linesToDelete = array_merge($this->linesToDelete, array_keys($lines));
        }
        $this->linesToAdd += $lines;

        return $this;
    }

    public function commit() : self
    {
        $this->validate();
        $this->prepare();
        $this->iterateThroughExistingLines();
        $this->iterateThroughRemainingLines();
        $this->ended();
        return $this;
    }

    /**
     * @throws FileIsNotWritable
     * @throws FileDoesNotExist
     * @throws FileIsNotReadable
     * @throws DirectoryIsNotWritable
     * @throws DirectoryDoesNotExist
     */
    protected function validate() : void
    {
        if ($this->linesToAdd) {
            $this->validateFileForWriting();
        }

        if ($this->linesToDelete) {
            $this->validateFileForDeleting();
        }

        if ($this->linesToGet) {
            $this->validateFileForReading();
        }
    }

    protected function prepare() : void 
    {
        $this->iterator  = new FileIterator($this->fileName);

        $this->lastLineToBeAdd = $this->linesToAdd 
            ? max(array_keys($this->linesToAdd))
            : 0;

        $this->lastLineToBeRetrieved = $this->linesToGet 
            ? max($this->linesToGet)
            : 0;

        $this->finalLine = $this->getNumberOfLinesToProcess();
        $this->tempFileCurrentLine = 0;

        if ($this->linesToAdd || $this->linesToDelete) {
            $this->tempFileName     = $this->tempFileName();
            $this->tempFileResource = fopen($this->tempFileName, 'w');
        }

        $this->linesRetrieved = array_combine(
            $this->linesToGet,
            array_fill(0, count($this->linesToGet), null)
        );
    }

    protected function lineToDelete(int $line) : bool
    {
        return in_array($line, $this->linesToDelete);
    }

    protected function lineToAdd(int $line) :? string
    {
        return isset($this->linesToAdd[$line]) 
            ? $this->linesToAdd[$line] 
            : NULL;
    }

    protected function justReading() : bool
    {
        return empty($this->linesToDelete) && empty($this->linesToAdd);
    }

    protected function iterateThroughExistingLines() : void
    {
        $this->iterator->rewind();

        while ($this->iterator->valid()) {
            if (in_array($this->iterator->currentLine, $this->linesToGet)) {
                $content = $this->iterator->current();
                $content = rtrim($content, "\n");
                $this->linesRetrieved[ $this->iterator->currentLine ] = $content;
            }

            if ($this->iterator->currentLine >= $this->lastLineToBeRetrieved && $this->justReading()) {
                break;
            }

            if ($this->lineToDelete($this->iterator->currentLine)) {
                $this->iterator->next();
                continue;
            }

            $toAdd = $this->lineToAdd($this->tempFileCurrentLine);
            if ($toAdd !== null && $this->tempFileResource) {
                $this->writeToTempFile($toAdd);
                continue;
            }

            if ($this->tempFileResource) {
                $this->writeToTempFile($this->iterator->current());
            }

            $this->iterator->next();
        }
    }

    protected function iterateThroughRemainingLines() : void
    {
        if (! $this->tempFileResource) {
            return;
        }

        while ($this->tempFileCurrentLine <= $this->finalLine && $this->tempFileCurrentLine <= $this->lastLineToBeAdd) {
            $toAdd = $this->lineToAdd($this->tempFileCurrentLine);
            $this->writeToTempFile((string) $toAdd);
        }
    }

    protected function ended() : void
    {
        if ($this->tempFileName) {
            fclose($this->tempFileResource);

            if (file_exists($this->fileName)) {
                unlink($this->fileName);
            }

            rename($this->tempFileName, $this->fileName);
        }
    }

    protected function getNumberOfLinesToProcess() : int
    {
        if (empty($this->linesToAdd) && empty($this->linesToDelete)) {
            return $this->linesToGet ? max($this->linesToGet) : 0;
        }

        return max(
            File::getLastLine($this->fileName, true) - 1,
            $this->linesToGet ? max($this->linesToGet) : 0,
            $this->linesToAdd ? max(array_keys($this->linesToAdd)) : 0
        );
    }

    protected function writeToTempFile(string $newContent) : void
    {
        $newContent = $this->sanitizeLine($newContent);
        fwrite($this->tempFileResource, $newContent);
        $this->tempFileCurrentLine++;
    }

    protected function sanitizeLine(string $content) : string
    {
        $string = (string) $content;
        $string = str_replace(["\n", "\r"], '', $string);
        $string .= "\n";
        return $string;
    }

    protected function validateFileForWriting() : void
    {
        $dir = dirname($this->fileName) . '/';

        if (! file_exists($dir)) {
            throw new DirectoryDoesNotExist($dir);
        }

        if (! is_writable($dir)) {
            throw new DirectoryIsNotWritable($dir);
        }

        if (file_exists($this->fileName) && !is_writable($this->fileName)) {
            throw new FileIsNotWritable($this->fileName);
        }
    }

    protected function validateFileForReading() : void
    {
        if (! file_exists($this->fileName)) {
            throw new FileDoesNotExist($this->fileName);
        }

        if (! is_readable($this->fileName)) {
            throw new FileIsNotReadable($this->fileName);
        }
    }

    protected function validateFileForDeleting() : void
    {
        if (! file_exists($this->fileName)) {
            throw new FileDoesNotExist($this->fileName);
        }

        if (! is_writable($this->fileName)) {
            throw new FileIsNotWritable($this->fileName);
        }
    }

    protected function tempFileName() : string
    {
        return $this->getTempDir() . uniqid() . '.tmp';
    }

    protected function getTempDir() : string
    {
        $tempDir = sys_get_temp_dir();
        return rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
