<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class EquipmentExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Equipments') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT
                `equipment`.`code`,
                `equipment`.`description`,
                `equipment`.`model`,
                `equipment_type`.`description` AS `equipment_type_description`,
                `equipment_location`.`description` AS `equipment_location_description`,
                `maintenance_plan`.`name` AS `maintenance_plan_name`,
                CONCAT(`user_responsible`.`firstname`, ', ', `user_responsible`.`lastname`) AS `user_responsible_name`,
                `equipment`.`is_active`,
                `equipment`.`notes`,
                `equipment`.`created_at`,
                CONCAT(`user_created_by`.`firstname`, ', ', `user_created_by`.`lastname`) AS `user_created_by_name`
            FROM `equipment`
            JOIN `equipment_type` ON `equipment_type`.`id` = `equipment`.`equipment_type_id`
            JOIN `equipment_location` ON `equipment_location`.`id` = `equipment`.`equipment_location_id`
            JOIN `user` AS user_responsible ON `user_responsible`.`id` = `equipment`.`responsible_id`
            JOIN `user` AS user_created_by ON `user_created_by`.`id` = `equipment`.`created_by`
            LEFT JOIN `maintenance_plan` ON `maintenance_plan`.`id` = `equipment`.`maintenance_plan_id`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['code'],
            $row['description'],
            $row['model'],
            $row['equipment_type_description'],
            $row['equipment_location_description'],
            $row['maintenance_plan_name'],
            $row['user_responsible_name'],
            $row['is_active'],
            $row['notes'],
            Date::PHPToExcel($row['created_at']),
            $row['user_created_by_name'],
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Code'),
            trans('Description'),
            trans('Model'),
            trans('Type'),
            trans('Location'),
            trans('Maintenance plan'),
            trans('Responsible'),
            trans('Is Active?'),
            trans('Notes'),
            trans('Created at'),
            trans('Created by'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}