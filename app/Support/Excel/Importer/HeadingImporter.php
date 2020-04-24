<?php

namespace App\Support\Excel\Importer;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use App\Excel\HeadingExtractor;

class HeadingImporter
{
    public function toArray($file)
    {
        $reader = new Xlsx;
        $reader->setReadDataOnly(true);
        
        $spreadsheet = $reader->load($file);
        
        $worksheet = $spreadsheet->getActiveSheet();

        return HeadingExtractor::extract($worksheet);
    }
}