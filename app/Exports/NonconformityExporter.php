<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class NonconformityExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Nonconformities') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `nc`.`id`,
                `nt`.`description` AS `type_description`,
                `nc`.`created_at`,
                CONCAT(`u1`.`firstname`, ' ', `u1`.`lastname`) AS `created_by_name`,
                `st`.`name` AS `system_name`,
                `pc`.`name` AS `process_name`,
                `nc`.`description`,
                `nc`.`inmmediate_actions`,
                CONCAT(`u2`.`firstname`, ' ', `u2`.`lastname`) AS `responsible_name`,
                `pd`.`code` AS `product_code`,
                `pd`.`description` AS `product_description`,
                `nc`.`quantity`,
                `nc`.`lot_number`,
                `nc`.`order_number`,
                `tr`.`created_at` AS `treatment_created_at`,
                CONCAT(`u3`.`firstname`, ' ', `u3`.`lastname`) AS `treatment_created_by_name`,
                `tt`.`description` AS `treatment_type_description`,
                `tr`.`observations` AS `treatment_observation`
            FROM `nonconformity` `nc`
            JOIN `nonconformity_type` `nt` ON `nt`.id = `nc`.`nonconformity_type_id`
            JOIN `system` `st` ON `st`.`id` = `nc`.`system_id`
            JOIN `process` `pc` ON `pc`.`id` = `nc`.`process_id`
            JOIN `user` `u1` ON `u1`.`id` = `nc`.`created_by`
            JOIN `user` `u2` ON `u2`.`id` = `nc`.`responsible_id`
            LEFT JOIN `product` `pd` ON `pd`.`id` = `nc`.`product_id`
            LEFT JOIN `nonconformity_treatment` `tr` ON `tr`.`nonconformity_id` = `nc`.`id`
            LEFT JOIN `user` `u3` ON `u3`.`id` = `tr`.`created_by`
            LEFT JOIN `nonconformity_treatment_type` `tt` ON `tr`.nonconformity_treatment_type_id = `tt`.`id`
            ORDER BY `nc`.`id`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['id'],
            Date::PHPToExcel($row['created_at']),
            $row['created_by_name'],
            $row['system_name'],
            $row['process_name'],
            $row['type_description'],
            $row['description'],
            $row['inmmediate_actions'],
            $row['responsible_name'],
            $row['product_code'] ?? null,
            $row['product_description'] ?? null,
            $row['quantity'],
            $row['lot_number'] ?? null,
            $row['order_number'] ?? null,
            Date::PHPToExcel($row['treatment_created_at'] ?? null) ?: null,
            $row['treatment_created_by_name'] ?? null,
            $row['treatment_type_description'] ?? null,
            $row['treatment_observation'] ?? null,
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Code'),
            trans('Created at'),
            trans('Created by'),
            trans('System'),
            trans('Process'),
            trans('Type'),
            trans('Description'),
            trans('Immediate actions'),
            trans('Responsible'),
            trans('Product') . ': ' . trans('Code'),
            trans('Product') . ': ' . trans('Description'),
            trans('Quantity'),
            trans('Lot number'),
            trans('Order number'),
            trans('Treatment') . ': ' . trans('Created at'),
            trans('Treatment') . ': ' . trans('Created by'),
            trans('Treatment') . ': ' . trans('Type'),
            trans('Treatment') . ': ' . trans('Observations'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}