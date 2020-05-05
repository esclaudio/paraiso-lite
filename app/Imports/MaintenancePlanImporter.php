<?php

namespace App\Imports;

use App\Models\MaintenancePlan;
use App\Support\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class MaintenancePlanImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Maintenance plans');
    }

    public function getFields(): array
    {
        return [
            'name' => [
                'title' => trans('Maintenance plan name'),
                'required' => true,
            ],

            'task' => [
                'title' => trans('Task'),
                'required' => true,
            ],

            'resources' => [
                'title' => trans('Resources'),
                'required' => false,
            ],

            'instructions' => [
                'title' => trans('Instructions'),
                'required' => false,
            ],

            'estimated_labor' => [
                'title' => trans('Estimated labor time'),
                'required' => false,
            ],

            'frequency_type' => [
                'title' => trans('Frequency type'),
                'required' => true,
            ],

            'frequency_value' => [
                'title' => trans('Frequency value'),
                'required' => true,
            ],
        ];
    }

    protected function model(array $row)
    {
        $plan = MaintenancePlan::firstOrCreate([
            'name' => $row['name'],
        ], [
            'created_by' => Auth::id(),
        ]);

        $existsTask = $plan->tasks()->where('description', $row['task'])->exists();
        
        if ( ! $existsTask) {
            $plan->tasks()->create([
                'description'     => $row['task'],
                'resources'       => $row['resources'],
                'instructions'    => $row['instructions'],
                'estimated_labor' => (int)$row['estimated_labor'],
                'frequency_type'  => strtolower($row['frequency_type']),
                'frequency_value' => (int)$row['frequency_value']
            ]);
        }

        return $plan;
    }
}