<?php declare(strict_types=1);

namespace App\Test;
use Iterator;

final class CsvFileIterator implements Iterator
{
    private $file;
    private $key = 0;
    private $current;
    private $count;

    public function __construct(string $file)
    {
        $this->file = fopen($file, 'r');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    public function rewind(): void
    {
        rewind($this->file);

        $this->current = fgetcsv($this->file);
        $this->key     = 0;
    }

    public function valid(): bool
    {
        return !feof($this->file);
    }

    public function key(): int
    {
        return $this->key;
    }

    public function current(): array
    {
        return $this->current;
    }

    public function next(): void
    {
        // Move to the next line
        $this->current = fgetcsv($this->file);
        $this->key++;

        // If we have hit a blank line, but not at the end of the file
        while(!feof($this->file) && $this->current === [null]) {
            $this->current = fgetcsv($this->file);
        }
    }
}
