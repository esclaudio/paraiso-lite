<?php

namespace App\Support\Excel;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HeadingExtractor
{
    public function extract(Worksheet $worksheet, int $startRow = 1): array
    {
        $rows = iterator_to_array($worksheet->getRowIterator($startRow, $startRow));
        
        /**
         * @var \PhpOffice\PhpSpreadsheet\Worksheet\Row $row
         */
        $row = reset($rows);

        return array_map(function ($cell) {
            return $cell->getValue();
        }, iterator_to_array($row->getCellIterator()));
    }
}