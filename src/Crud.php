<?php

namespace AdinanCenci\FileEditor;

use AdinanCenci\FileEditor\Exception\DirectoryDoesNotExist;
use AdinanCenci\FileEditor\Exception\DirectoryIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileIsNotWritable;
use AdinanCenci\FileEditor\Exception\FileDoesNotExist;
use AdinanCenci\FileEditor\Exception\FileIsNotReadable;

/**
 * Create, read, update, delete.
 */
class Crud
{
    /**
     * @var string
     *   Absolute path to the file.
     */
    protected string $fileName = '';

    /**
     * @var AdinanCenci\FileEditor\FileIterator
     *   Object to iterate through the file.
     */
    protected FileIterator $iterator;

    /**
     * @var string
     *   Name of the temporary file used to apply the changes to.
     */
    protected string $tempFileName = '';

    /**
     * @var resource
     *   File resource used to edit the temporary file.
     */
    protected $tempFileResource = null;

    /**
     * @var int
     *   Current line of the temporary file.
     */
    protected int $tempFileCurrentLine = 0;

    /**
     * @var int
     *   Property to controll the loop.
     */
    protected int $finalLine = 0;

    /**
     * @var int[]
     *   Lines to be retrieved.
     */
    protected array $linesToGet = [];

    /**
     * @var int[]
     *   Lines to be deleted.
     */
    protected array $linesToDelete = [];

    /**
     * @var string[]
     *   Lines to be added to the file indexed by their position.
     */
    protected array $linesToAdd = [];

    /**
     * @var int
     *   The last line to be added.
     */
    protected $lastLineToBeAdd = 0;

    /**
     * @var (string|null)[]
     *   Lines retrieved.
     */
    protected array $linesRetrieved = [];

    /**
     * @var int
     *   The last line to be retrieved.
     */
    protected $lastLineToBeRetrieved = 0;

    /**
     * Constructor.
     *
     * @param string $fileName
     *   Absolute path to the file.
     */
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
     * Provices a list of lines to be retrieved from the file.
     *
     * @param int[] $lines
     *   The lines we are aiming for.
     */
    public function get(array $lines): self
    {
        $this->linesToGet = $lines;
        return $this;
    }

    /**
     * Provides a list of lines to be deleted.
     *
     * @param int[] $lines
     *   The lines we wish to delete.
     */
    public function delete(array $lines): self
    {
        $this->linesToDelete = array_merge($this->linesToDelete, $lines);
        return $this;
    }

    /**
     * Provides a list of lines to be added to the file.
     *
     * No content will be lost, just added.
     *
     * @param string[] $lines
     *   New content to be added.
     */
    public function add(array $lines): self
    {
        $this->linesToAdd += $lines;
        return $this;
    }

    /**
     * Provides a list of lines to be set in the file.
     *
     * It will override existing content, unlike ::add().
     *
     * @param string[] $lines
     *   Content to be set.
     */
    public function set(array $lines): self
    {
        if (file_exists($this->fileName)) {
            $this->linesToDelete = array_merge($this->linesToDelete, array_keys($lines));
        }
        $this->linesToAdd += $lines;

        return $this;
    }

    /**
     * Commits the changes.
     */
    public function commit(): self
    {
        $this->validate();
        $this->prepare();
        $this->iterateThroughExistingLines();
        $this->iterateThroughRemainingLines();
        $this->ended();

        return $this;
    }

    /**
     * @throws AdinanCenci\FileEditor\Exception\FileIsNotWritable
     * @throws AdinanCenci\FileEditor\Exception\FileDoesNotExist
     * @throws AdinanCenci\FileEditor\Exception\FileIsNotReadable
     * @throws AdinanCenci\FileEditor\Exception\DirectoryIsNotWritable
     * @throws AdinanCenci\FileEditor\Exception\DirectoryDoesNotExist
     */
    protected function validate(): void
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

    protected function prepare(): void
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

        if (! $this->justReading()) {
            // but editing.
            $this->tempFileName     = $this->generateTempFileName();
            $this->tempFileResource = fopen($this->tempFileName, 'w');
        }

        $this->linesRetrieved = array_combine(
            $this->linesToGet,
            array_fill(0, count($this->linesToGet), null)
        );
    }

    /**
     * Checks if $line should be deleted.
     *
     * @param int $line
     *   The line in the file.
     *
     * @return bool
     *   If it should be deleted.
     */
    protected function lineToDelete(int $line): bool
    {
        return in_array($line, $this->linesToDelete);
    }

    /**
     * Checks if $line is to be added to the file.
     *
     * @param int $line
     *   The line in the file.
     *
     * @return string|null
     *   The content to be added, null if there is not assigned to the $line.
     */
    protected function lineToAdd(int $line): ?string
    {
        return isset($this->linesToAdd[$line])
            ? $this->linesToAdd[$line]
            : null;
    }

    /**
     * Checks if we are just reading and not editing the file.
     *
     * @return bool
     *   True if just reading.
     */
    protected function justReading(): bool
    {
        return empty($this->linesToDelete) && empty($this->linesToAdd);
    }

    protected function iterateThroughExistingLines(): void
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

    protected function iterateThroughRemainingLines(): void
    {
        if (! $this->tempFileResource) {
            return;
        }

        while ($this->tempFileCurrentLine <= $this->finalLine && $this->tempFileCurrentLine <= $this->lastLineToBeAdd) {
            $toAdd = $this->lineToAdd($this->tempFileCurrentLine);
            $this->writeToTempFile((string) $toAdd);
        }
    }

    protected function ended(): void
    {
        if (!$this->tempFileName) {
            return;
        }

        fclose($this->tempFileResource);

        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }

        rename($this->tempFileName, $this->fileName);
    }

    protected function getNumberOfLinesToProcess(): int
    {
        if ($this->justReading()) {
            return $this->linesToGet
                ? max($this->linesToGet)
                : 0;
        }

        return max(
            File::getLastLine($this->fileName, true) - 1,
            $this->linesToGet ? max($this->linesToGet) : 0,
            $this->linesToAdd ? max(array_keys($this->linesToAdd)) : 0
        );
    }

    /**
     * Writes content to the temporary file handle.
     *
     * @param string $newContent
     *   String to be added to the tempfile.
     */
    protected function writeToTempFile(string $newContent): void
    {
        $newContent = $this->sanitizeLine($newContent);
        fwrite($this->tempFileResource, $newContent);
        $this->tempFileCurrentLine++;
    }

    /**
     * Sanitizes a string.
     *
     * Stripes it of line breaks, adds a single line break th the end of it.
     *
     * @param string $content
     *   The content to be sanitized.
     *
     * @return string
     *   The sanitized content.
     */
    protected function sanitizeLine(string $content): string
    {
        $string = (string) $content;
        $string = str_replace(["\n", "\r"], '', $string);
        $string .= "\n";
        return $string;
    }

    /**
     * Validates the file for writting.
     *
     * Throws exceptions depending on whatever problem we may be facing.
     */
    protected function validateFileForWriting(): void
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

    /**
     * Validates the file for reading.
     *
     * Throws exceptions depending on whatever problem we may be facing.
     */
    protected function validateFileForReading(): void
    {
        if (! file_exists($this->fileName)) {
            throw new FileDoesNotExist($this->fileName);
        }

        if (! is_readable($this->fileName)) {
            throw new FileIsNotReadable($this->fileName);
        }
    }

    /**
     * Validates the file for deleting.
     *
     * Throws exceptions depending on whatever problem we may be facing.
     */
    protected function validateFileForDeleting(): void
    {
        if (! file_exists($this->fileName)) {
            throw new FileDoesNotExist($this->fileName);
        }

        if (! is_writable($this->fileName)) {
            throw new FileIsNotWritable($this->fileName);
        }
    }

    /**
     * Generates a random name for temporary files.
     *
     * @return string
     *   An absolute path.
     */
    protected function generateTempFileName(): string
    {
        return $this->getTempDir() . uniqid() . '.tmp';
    }

    /**
     * Returns the system's tempoary directory.
     *
     * @return string
     *   Absolute path.
     */
    protected function getTempDir(): string
    {
        $tempDir = sys_get_temp_dir();
        return rtrim($tempDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
