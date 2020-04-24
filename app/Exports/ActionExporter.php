<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class ActionExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Actions') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `ac`.`id`,
                `at`.`description` AS `type_description`,
                CONCAT(`at`.`prefix`, '-', LPAD(`ac`.`number`, 4, '0')) AS `code`,
                `ac`.`created_at`,
                CONCAT(`u1`.`firstname`, ' ', `u1`.`lastname`) AS `created_by_name`,
                `st`.`name` AS `system_name`,
                `pc`.`name` AS `process_name`,
                `sc`.`description` AS `source_description`,
                `ac`.`title`,
                `ac`.`description`,
                CONCAT(`u2`.`firstname`, ' ', `u2`.`lastname`) AS `responsible_name`,
                CONCAT(`u3`.`firstname`, ' ', `u3`.`lastname`) AS `analyzer_name`,
                `cu`.`code` AS `customer_code`,
                `cu`.`name` AS `customer_name`,
                `pd`.`code` AS `product_code`,
                `pd`.`description` AS `product_description`,
                `ac`.`quantity`,
                `aa`.`answer_1` AS `analysis_answer_1`,
                `aa`.`answer_2` AS `analysis_answer_2`,
                `aa`.`answer_3` AS `analysis_answer_3`,
                `aa`.`answer_4` AS `analysis_answer_4`,
                `aa`.`answer_5` AS `analysis_answer_5`,
                `aa`.`root_cause` AS `analysis_root_cause`,
                `av`.`expiration_date` AS `verification_due_date`,
                CONCAT(`u4`.`firstname`, ' ', `u4`.`lastname`) AS `verification_responsible_name`,
                `ar`.`created_at` AS `result_created_at`,
                CONCAT(`u5`.`firstname`, ' ', `u5`.`lastname`) AS `result_created_by_name`,
                `ar`.`details` AS `result_details`,
                `ar`.`is_effective` AS `result_is_effective`,
                `ar`.`risk_review` AS `result_risk_review`,
                `ar`.`system_changes` AS `result_system_changes`
            FROM `action` `ac`
            JOIN `action_type` at ON `at`.id = `ac`.`action_type_id`
            JOIN `system` `st` ON `st`.id = `ac`.`system_id`
            JOIN `process` `pc` ON `pc`.id = `ac`.`process_id`
            JOIN `source` `sc` ON `sc`.id = `ac`.`source_id`
            JOIN `user` `u1` ON `u1`.id = `ac`.`created_by`
            JOIN `user` `u2` ON `u2`.id = `ac`.`responsible_id`
            JOIN `user` `u3` ON `u3`.id = `ac`.`analyzer_id`
            LEFT JOIN `customer` `cu` ON `cu`.id = `ac`.`customer_id`
            LEFT JOIN `product` `pd` ON `pd`.id = `ac`.`product_id`
            LEFT JOIN `action_analysis` `aa` ON `aa`.`action_id` = `ac`.`id`
            LEFT JOIN `action_verification` `av` ON `av`.`action_id` = `ac`.`id`
            LEFT JOIN `action_result` `ar` ON `ar`.action_id = `ac`.`id`
            LEFT JOIN `user` `u4` ON `u4`.id = `av`.`responsible_id`
            LEFT JOIN `user` `u5` ON `u5`.id = `ar`.`created_by`
            ORDER BY `ac`.`id`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['code'],
            Date::PHPToExcel($row['created_at']),
            $row['system_name'],
            $row['process_name'],
            $row['type_description'],
            $row['source_description'],
            $row['title'],
            $row['description'],
            $row['responsible_name'],
            $row['analyzer_name'],
            $row['customer_code'] ?? null,
            $row['customer_name'] ?? null,
            $row['product_code'] ?? null,
            $row['product_description'] ?? null,
            $row['quantity'],
            $row['analysis_answer_1'] ?? null,
            $row['analysis_answer_2'] ?? null,
            $row['analysis_answer_3'] ?? null,
            $row['analysis_answer_4'] ?? null,
            $row['analysis_answer_5'] ?? null,
            $row['analysis_root_cause'] ?? null,
            Date::PHPToExcel($row['verification_due_date'] ?? null) ?: null,
            $row['verification_responsible_name'] ?? null,
            Date::PHPToExcel($row['result_created_at'] ?? null) ?: null,
            $row['result_created_by_name'] ?? null,
            $row['result_details'] ?? null,
            $row['result_is_effective'] ?? null,
            $row['result_risk_review'] ?? null,
            $row['result_system_changes'] ?? null,
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Code'),
            trans('Created at'),
            trans('System'),
            trans('Process'),
            trans('Type'),
            trans('Source'),
            trans('Title'),
            trans('Description'),
            trans('Responsible'),
            trans('Analyzer'),
            trans('Customer') . ': ' . trans('Code'),
            trans('Customer') . ': ' . trans('Name'),
            trans('Product') . ': ' . trans('Code'),
            trans('Product') . ': ' . trans('Description'),
            trans('Quantity'),
            trans('Analysis') . ': ' . trans('Answer 1'),
            trans('Analysis') . ': ' . trans('Answer 2'),
            trans('Analysis') . ': ' . trans('Answer 3'),
            trans('Analysis') . ': ' . trans('Answer 4'),
            trans('Analysis') . ': ' . trans('Answer 5'),
            trans('Analysis') . ': ' . trans('Root cause'),
            trans('Verification') . ': ' . trans('Due date'),
            trans('Verification') . ': ' . trans('Responsible'),
            trans('Result') . ': ' . trans('Created at'),
            trans('Result') . ': ' . trans('Created by'),
            trans('Result') . ': ' . trans('Details'),
            trans('Result') . ': ' . trans('Is effective'),
            trans('Result') . ': ' . trans('R/O review'),
            trans('Result') . ': ' . trans('System changes'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'X' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}