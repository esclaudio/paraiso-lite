<?php

namespace App\Support\Excel;

use Slim\Http\Stream;
use Slim\Http\Response;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Excel\Importer\Importer;

class ImportFileSample
{
    /**
     * Importer
     *
     * @var \App\Importer\Importer
     */
    protected $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;    
    }

    public function download(Response $response): Response
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $column = 'A';
        $row = 1;

        foreach ($this->importer->getFields() as $field) {
            $sheet->getCell("{$column}{$row}")->setValue($field['title']);
            $column++;
        }
        
        $tempname = tempnam(sys_get_temp_dir(), get_class($this));
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempname);
        
        $tempfile = fopen($tempname, 'rb');
        $stream = new Stream($tempfile);

        $filename = $this->importer->getTitle() . '.xlsx';

        return $response->withBody($stream)
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('Content-Encoding', 'none')
            ->withHeader('Content-Description', 'File Transfer');
    }
}