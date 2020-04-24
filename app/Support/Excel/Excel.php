<?php

namespace App\Support\Excel;

use Slim\Http\Stream;
use Slim\Http\Response;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use App\Excel\HeadingExtractor;
use App\Excel\Exporter\Exporter;
use App\Excel\Importer\Importer;

class Excel
{
    public function import(Importer $importer, string $filePath, int $startRow = 1, int $limit = 0)
    {
        $rows = $this->toArray($filePath, $startRow, $limit);
        $importer->import($rows);
    }

    public function toArray(string $filePath, int $startRow = 1, int $limit = 0): array
    {
        $reader = new XlsxReader;
        $reader->setReadDataOnly(true);
        
        $spreadsheet = $reader->load($filePath);

        $worksheet = $spreadsheet->getActiveSheet();

        $headings = HeadingExtractor::extract($worksheet, $startRow);

        $rows = [];

        foreach ($worksheet->getRowIterator($startRow + 1) as $spreadsheetRow) {
            $row = [];
            $col = 'A';

            foreach ($spreadsheetRow->getCellIterator() as $spreadsheetCell) {
                $value = trim($spreadsheetCell->getCalculatedValue()) ?: null;

                if (isset($headings[$col])) {
                    $row[$headings[$col]] = $value;
                } else {
                    $row[] = $value;
                }

                $col++;
            }

            $rows[] = $row;

            if ($limit && count($rows) >= $limit) {
                break;
            }
        }

        return $rows;
    }

    public function download(Exporter $exporter, Response $response): Response
    {
        $name = $exporter->getTitle() . '.xlsx';
        
        $filePath = tempnam(sys_get_temp_dir(), get_class($exporter));
        
        $this->store($exporter, $filePath);
        
        $file = fopen($filePath, 'rb');
        $stream = new Stream($file);

        return $response->withBody($stream)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $name . '"')
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Encoding', 'none')
            ->withHeader('Content-Description', 'File Transfer');
    }

    public function store(Exporter $exporter, string $filePath)
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        $exporter->write($worksheet);
        
        $writer = new XlsxWriter($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($filePath);
    }
}