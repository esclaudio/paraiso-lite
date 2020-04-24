<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Excel\Exporter\PDOExporter;

class RiskExporter extends PDOExporter
{
    public function getTitle(): string
    {
        return trans('Risks and oportunities') . '_' . date('Ymd'); 
    }

    protected function getQuery(): string
    {
        return "
            SELECT 
                CONCAT('RO-', LPAD(`risk`.`id`, 4, '0')) AS code,
                `risk_type`.`name` AS `type_name`,
                `risk`.`created_at`,
                CONCAT(`user_creator`.`firstname`, ' ', `user_creator`.`lastname`) AS `creator_name`,
                `system`.`name` AS `system_name`,
                `process`.`name` AS `process_name`,
                `source`.`description` AS `source_description`,
                `risk`.`description`,
                `risk`.`impact`,
                CONCAT(`user_responsible`.`firstname`, ' ', `user_responsible`.`lastname`) AS `responsible_name`,
                `analysis_risk_likelihood`.`name` AS `analysis_likelihood_name`,
                `analysis_risk_consequence`.`name` AS `analysis_consequence_name`,
                `analysis_risk_level`.`name` AS `analysis_level_name`,
                `risk`.`observations` AS `analysis_observations`,
                `risk_treatment_type`.`name` AS `analysis_treatment_type_name`,
                `assessment_risk_likelihood`.`name` AS `assessment_likelihood_name`,
                `assessment_risk_consequence`.`name` AS `assessment_consequence_name`,
                `assessment_risk_level`.`name` AS `assessment_level_name`,
                `risk_assessment`.`conclusions` AS `assessment_conclusions`
            FROM `risk`
            JOIN `risk_type` ON `risk_type`.`id` = `risk`.`risk_type_id`
            JOIN `system` ON `system`.`id` = `risk`.`system_id`
            JOIN `process` ON `process`.`id` = `risk`.`process_id`
            JOIN `user` `user_creator` ON `user_creator`.`id` = `risk`.`created_by`
            JOIN `user` `user_responsible` ON `user_responsible`.`id` = `risk`.`responsible_id`
            LEFT JOIN `source` ON `source`.id = `risk`.`source_id`
            LEFT JOIN `risk_treatment_type` ON `risk_treatment_type`.id = `risk`.`risk_treatment_type_id`
            LEFT JOIN `risk_likelihood` `analysis_risk_likelihood` ON `analysis_risk_likelihood`.`id` = `risk`.`risk_likelihood_id`
            LEFT JOIN `risk_consequence` `analysis_risk_consequence` ON `analysis_risk_consequence`.`id` = `risk`.`risk_consequence_id`
            LEFT JOIN `risk_level` `analysis_risk_level` ON `analysis_risk_level`.`id` = `risk`.`risk_level_id`
            LEFT JOIN `risk_assessment` ON `risk`.`id` = `risk_assessment`.`risk_id`
            LEFT JOIN `risk_likelihood` `assessment_risk_likelihood` ON `assessment_risk_likelihood`.`id` = `risk_assessment`.`risk_likelihood_id`
            LEFT JOIN `risk_consequence` `assessment_risk_consequence` ON `assessment_risk_consequence`.`id` = `risk_assessment`.`risk_consequence_id`
            LEFT JOIN `risk_level` `assessment_risk_level` ON `assessment_risk_level`.`id` = `risk_assessment`.`risk_level_id`
            ORDER BY `risk`.`id`
        ";

        return null;
    }

    protected function map($row): array
    {
        return [
            $row['code'],
            Date::PHPToExcel($row['created_at']),
            $row['creator_name'],
            $row['type_name'],
            $row['system_name'],
            $row['process_name'],
            $row['source_description'],
            $row['description'],
            $row['impact'],
            $row['responsible_name'],
            $row['analysis_likelihood_name'] ?? null,
            $row['analysis_consequence_name'] ?? null,
            $row['analysis_level_name'] ?? null,
            $row['analysis_observations'] ?? null,
            $row['analysis_treatment_type_name'] ?? null,
            $row['assessment_likelihood_name'] ?? null,
            $row['assessment_consequence_name'] ?? null,
            $row['assessment_level_name'] ?? null,
            $row['assessment_conclusions'] ?? null,
        ];
    }

    protected function getHeadings(): array
    {
        return [
            trans('Code'),
            trans('Created at'),
            trans('Creator'),
            trans('Type'),
            trans('System'),
            trans('Process'),
            trans('Source'),
            trans('Description'),
            trans('Impact'),
            trans('Responsible'),
            trans('Analysis') . ': ' . trans('Likelihood'),
            trans('Analysis') . ': ' . trans('Consequence'),
            trans('Analysis') . ': ' . trans('Level'),
            trans('Analysis') . ': ' . trans('Observations'),
            trans('Analysis') . ': ' . trans('Treatment'),
            trans('Result') . ': ' . trans('Likelihood'),
            trans('Result') . ': ' . trans('Consequence'),
            trans('Result') . ': ' . trans('Level'),
            trans('Result') . ': ' . trans('Conclusions'),
        ];
    }

    protected function getColumnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}