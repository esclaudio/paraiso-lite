<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class SourceExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Sources') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `description`,
                `created_at`
            FROM `source`
            ORDER BY `id`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['description'],
            Date::PHPToExcel($row['created_at']),
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Description'),
            trans('Created at'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}