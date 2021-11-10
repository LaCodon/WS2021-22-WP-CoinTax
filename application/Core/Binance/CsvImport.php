<?php

namespace Core\Binance;

final class CsvImport
{
    private $_handle;
    private $_rowCount = 0;

    public function __construct(
        private string $_filepath
    )
    {
    }

    public function open(): bool
    {
        $this->_handle = fopen($this->_filepath, "r");
        if ($this->_handle === false) {
            return false;
        }

        return true;
    }

    public function skipHeader(): void
    {
        if ($this->_rowCount === 0) {
            fgets($this->_handle, 1000);
            ++$this->_rowCount;
        }
    }

    public function getNextLine(): array|null
    {
        $data = fgets($this->_handle, 1000);
        if ($data === false) {
            return null;
        }

        // binance encloses with "" instead of " and wraps the whole line in single " -> clean this up
        $data = str_replace('""', '##', $data);
        $data = str_replace('"', '', $data);
        $data = str_replace('##', '"', $data);

        ++$this->_rowCount;

        $data = str_getcsv($data);

        return [
            'date_str' => $data[0],
            'pair' => $data[1],
            'side' => $data[2],
            'price' => $data[3],
            'executed' => $data[4],
            'amount' => $data[5],
            'fee' => $data[6],
        ];
    }

    public function __destruct()
    {
        fclose($this->_handle);
    }

}