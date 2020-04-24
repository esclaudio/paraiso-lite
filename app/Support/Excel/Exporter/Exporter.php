<?php

namespace App\Support\Excel\Exporter;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Illuminate\Support\Collection;

abstract class Exporter
{
    /**
     * Collection
     *
     * @return Collection
     */
    protected abstract function getCollection(): Collection;

    /**
     * Title
     *
     * @return void
     */
    public function getTitle()
    {
        return str_replace('Exporter', '', get_class($this)) . '_' . date('Ymd');
    }

    /**
     * Write to worksheet
     *
     * @param Worksheet $worksheet
     * @param string $startColumn
     * @param integer $startRow
     * 
     * @return void
     */
    public function write(Worksheet $worksheet, string $startColumn = 'A', int $startRow = 1, bool $autosize = false)
    {
        $this->writeHeadings($worksheet, $startColumn, $startRow);
        $this->writeRows($worksheet, $startColumn, $startRow + 1);
        
        foreach ($this->getColumnFormats() as $column => $format) {
            $this->formatColumn($worksheet, $column, $format);
        }

        if ($autosize) {
            $this->autoSize($worksheet);
        }
    }

    /**
     * Autosize
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * 
     * @return void
     */
    public function autoSize(Worksheet $worksheet)
    {
        foreach ($this->buildColumnRange('A', $worksheet->getHighestDataColumn()) as $col) {
            $worksheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Format column
     * 
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param string $column
     * @param string $format
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function formatColumn(Worksheet $worksheet, string $column, string $format)
    {
        $worksheet
            ->getStyle($column . '1:' . $column . $worksheet->getHighestRow())
            ->getNumberFormat()
            ->setFormatCode($format);
    }

    /**
     * Write headings
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param string $startColumn
     * @param integer $startRow
     * 
     * @return void
     */
    protected function writeHeadings(Worksheet $worksheet, string $startColumn, int $startRow)
    {
        $worksheetColumn = $startColumn;
        $worksheetRow = $startRow;

        foreach ($this->getHeadings() as $heading) {
            $cell = $worksheet->getCell("{$worksheetColumn}{$worksheetRow}");
            $cell->setValue($heading);

            $this->formatHeading($cell);

            $worksheetColumn++;
        }
    }

    /**
     * Write rows
     *
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet
     * @param string $startColumn
     * @param integer $startRow
     * 
     * @return void
     */
    protected function writeRows(Worksheet $worksheet, string $startColumn, int $startRow)
    {
        foreach ($this->getRows() as $i => $row) {
            $worksheetColumn = $startColumn;
            $worksheetRow = $startRow + $i;

            foreach ($row as $field => $value) {
                $cell = $worksheet->getCell("{$worksheetColumn}{$worksheetRow}");
                $cell->setValue($value);

                $this->formatCell($cell, $field);

                $worksheetColumn++;
            }
        }
    }

    /**
     * Rows
     *
     * @return array
     */
    protected function getRows(): array
    {
        $rows = [];

        foreach($this->getCollection() as $row) {
            $rows[] = $this->map($row);
        }
        
        return $rows;
    }

    /**
     * Map
     *
     * @param mixed $row
     * 
     * @return array
     */
    protected function map($row): array
    {
        // When dealing with eloquent models, we'll skip the relations
        // as we won't be able to display them anyway.
        if (method_exists($row, 'attributesToArray')) {
            return $row->attributesToArray();
        }

        if ($row instanceof Arrayable) {
            return $row->toArray();
        }

        // Convert StdObjects to arrays
        if (is_object($row)) {
            return json_decode(json_encode($row), true);
        }

        return $row;
    }

    /**
     * Headings
     *
     * @return array
     */
    protected function getHeadings(): array
    {
        return [];
    }

    /**
     * Column formats
     *
     * @return array
     */
    protected function getColumnFormats(): array
    {
        return [];
    }
    
    /**
     * Format heading
     *
     * @param Cell $cell
     * 
     * @return void
     */
    protected function formatHeading(Cell $cell)
    {
        $cell->getStyle()->getFont()->setBold(true);
    }

    /**
     * Format cell
     *
     * @param Cell $cell
     * @param string $field
     * 
     * @return void
     */
    protected function formatCell(Cell $cell, string $field)
    {
        //
    }

    /**
     * @param string $lower
     * @param string $upper
     *
     * @return \Generator
     */
    protected function buildColumnRange(string $lower, string $upper)
    {
        $upper++;
        for ($i = $lower; $i !== $upper; $i++) {
            yield $i;
        }
    }
}