<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class InterestedPartyExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Interested parties') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `interested_party`.`name`,
                `interested_party`.`requirements`,
                `interested_party`.`verification`,
                `interested_party`.`created_at`,
                CONCAT(`user_created_by`.`firstname`, ', ', `user_created_by`.`lastname`) AS `user_created_by_name`,
                `interested_party`.`updated_at`,
                CONCAT(`user_updated_by`.`firstname`, ', ', `user_updated_by`.`lastname`) AS `user_updated_by_name`
            FROM `interested_party`
            JOIN `user` AS user_created_by ON `user_created_by`.`id` = `interested_party`.`created_by`
            LEFT JOIN `user` AS user_updated_by ON `user_updated_by`.`id` = `interested_party`.`updated_by`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['name'],
            $row['requirements'],
            $row['verification'],
            Date::PHPToExcel($row['created_at']),
            $row['user_created_by_name'],
            Date::PHPToExcel($row['updated_at']),
            $row['user_updated_by_name'],
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Name'),
            trans('Requirements'),
            trans('Verification'),
            trans('Created at'),
            trans('Created by'),
            trans('Updated at'),
            trans('Updated by'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}