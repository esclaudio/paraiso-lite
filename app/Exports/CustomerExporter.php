<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class CustomerExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Customers') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `code`,
                `name`,
                `created_at`
            FROM `customer`
            ORDER BY `id`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['code'],
            $row['name'],
            Date::PHPToExcel($row['created_at']),
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Code'),
            trans('Name'),
            trans('Created at'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}