<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class ComplaintExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Complaints') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `cm`.`id`,
                `cm`.`created_at`,
                CONCAT(`u1`.`firstname`, ' ', `u1`.`lastname`) AS `created_by_name`,
                `st`.`name` AS `system_name`,
                `pc`.`name` AS `process_name`,
                `cm`.`description`,
                `cm`.`verification`,
                `cm`.`inmmediate_actions`,
                CONCAT(`u2`.`firstname`, ' ', `u2`.`lastname`) AS `responsible_name`,
                `cm`.`contact`,
                `cu`.`code` AS `customer_code`,
                `cu`.`name` AS `customer_name`,
                `pd`.`code` AS `product_code`,
                `pd`.`description` AS `product_description`,
                `cm`.`quantity`,
                `cc`.`description` AS `category_description`,
                `cm`.`is_company_responsibility`,
                `cm`.`conclusions`,
                `cm`.`cost`,
                `cm`.`closed_at`,
                CONCAT(`u3`.`firstname`, ' ', `u3`.`lastname`) AS `closed_by_name`,
                `v`.`actions`,
                `v`.`root_causes`
            FROM `complaint` `cm`
            JOIN `system` `st` ON `st`.`id` = `cm`.`system_id`
            JOIN `process` `pc` ON `pc`.`id` = `cm`.`process_id`
            JOIN `user` `u1` ON `u1`.`id` = `cm`.`created_by`
            JOIN `user` `u2` ON `u2`.`id` = `cm`.`responsible_id`
            LEFT JOIN `complaint_category` `cc` ON `cc`.id = `cm`.`complaint_category_id`
            LEFT JOIN `customer` `cu` ON `cu`.id = `cm`.`customer_id`
            LEFT JOIN `product` `pd` ON `pd`.`id` = `cm`.`product_id`
            LEFT JOIN `user` `u3` ON `u3`.`id` = `cm`.`closed_by`
            LEFT JOIN `v_action_by_complaint` AS `v` ON `v`.`complaint_id` = `cm`.`id`
            ORDER BY `cm`.`id`
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
            $row['description'],
            $row['verification'],
            $row['inmmediate_actions'],
            $row['responsible_name'],
            $row['contact'] ?? null,
            $row['customer_code'] ?? null,
            $row['customer_name'] ?? null,
            $row['product_code'] ?? null,
            $row['product_description'] ?? null,
            $row['quantity'],
            $row['category_description'] ?? null,
            $row['is_company_responsibility'] ?? null,
            $row['conclusions'] ?? null,
            $row['cost'] ?? null,
            Date::PHPToExcel($row['closed_at'] ?? null) ?: null,
            $row['closed_by_name'] ?? null,
            $row['actions'] ?? null,
            $row['root_causes'] ?? null,
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
            trans('Description'),
            trans('Verification'),
            trans('Immediate actions'),
            trans('Responsible'),
            trans('Contact'),
            trans('Customer') . ': ' . trans('Code'),
            trans('Customer') . ': ' . trans('Name'),
            trans('Product') . ': ' . trans('Code'),
            trans('Product') . ': ' . trans('Description'),
            trans('Quantity'),
            trans('Category'),
            trans('Is it our responsibility?'),
            trans('Conclusions'),
            trans('Cost'),
            trans('Closed at'),
            trans('Closed by'),
            trans('Actions'),
            trans('Root cause'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'T' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}