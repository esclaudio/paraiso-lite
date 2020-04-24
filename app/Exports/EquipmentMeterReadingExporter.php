<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Collection;
use App\Excel\Exporter\PDOExporter;
use App\Models\EquipmentMeter;

class EquipmentMeterReadingExporter extends PDOExporter
{
    protected $equipments;
    protected $meter;

    public function forEquipments(Collection $equipments)
    {
        $ids = $equipments->pluck('id')->toArray();

        $this->equipments = array_unique(array_map('intval', $ids));

        return $this;
    }

    public function forMeter(EquipmentMeter $meter)
    {
        $this->meter = $meter;

        return $this;
    }

    public function getTitle(): string
    {
        return trans('Equipment meter readings'). ' ' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        $where = '1';

        // TODO: Use prepared statements (avoid SQL Injection)

        if ($this->meter) {
            $where .= ' AND `m`.`id` = ' . (int)$this->meter->id;
        }

        if ($this->equipments) {
            $where .= ' AND `e`.`id` IN (' . implode(',', $this->equipments) . ')';
        }

        return "
            SELECT
                `e`.`code` AS equipment_code,
                `e`.`description` AS equipment_description,
                `m`.`description` AS meter_description,
                `r`.`created_at` AS reading_created_at,
                CONCAT(`u`.`firstname`, ' ', `u`.`lastname`) AS `created_by_name`,
                `r`.`value` AS reading_value
            FROM `equipment` `e`
            JOIN `equipment_meter` `m` ON `m`.`equipment_id` = `e`.`id`
            JOIN `equipment_meter_reading` `r` ON `r`.`equipment_meter_id` = `m`.`id`
            JOIN `user` `u` ON `u`.id = `r`.`created_by`
            WHERE {$where}
            ORDER BY `e`.`code`, `m`.`description`, `r`.`value`
        ";
    }

    protected function map($row): array
    {
        return [
            $row['equipment_code'],
            $row['equipment_description'],
            $row['meter_description'],
            Date::PHPToExcel($row['reading_created_at']),
            $row['created_by_name'],
            $row['reading_value'],
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Equipment code'),
            trans('Equipment description'),
            trans('Meter'),
            trans('Created at'),
            trans('Created by'),
            trans('Value'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}