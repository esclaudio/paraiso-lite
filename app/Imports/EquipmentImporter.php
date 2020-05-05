<?php

namespace App\Imports;

use App\Models\MaintenancePlan;
use App\Models\EquipmentType;
use App\Models\EquipmentLocation;
use App\Models\Equipment;
use App\Support\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class EquipmentImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Equipments');
    }

    public function getFields(): array
    {
        return [
            'code' => [
                'title' => trans('Code'),
                'required' => true,
            ],

            'description' => [
                'title' => trans('Description'),
                'required' => true,
            ],

            'model' => [
                'title' => trans('Model'),
                'required' => false,
            ],

            'type' => [
                'title' => trans('Type'),
                'required' => true,
            ],

            'location' => [
                'title' => trans('Location'),
                'required' => true,
            ],

            'notes' => [
                'title' => trans('Notes'),
                'required' => false,
            ],

            'maintenance_plan' => [
                'title' => trans('Maintenance plan'),
                'required' => false,
            ],
        ];
    }

    protected function model(array $row)
    {
        if (Equipment::where('code', $row['code'])->exists()) {
            return null;
        }
        
        $userId = Auth::id();

        $type = EquipmentType::firstOrCreate([
            'description' => $row['type']
        ], [
            'created_by' => $userId,
        ]);

        $location = EquipmentLocation::firstOrCreate([
            'description' => $row['location']
        ], [
            'created_by' => $userId,
        ]);

        $plan = MaintenancePlan::firstOrCreate([
            'name' => $row['maintenance_plan']
        ], [
            'created_by' => $userId,
        ]);

        return new Equipment([
            'code'                  => $row['code'],
            'description'           => $row['description'],
            'model'                 => $row['model'],
            'equipment_type_id'     => $type->id,
            'equipment_location_id' => $location->id,
            'maintenance_plan_id'   => $plan->id,
            'notes'                 => $row['notes'],
            'created_by'            => $userId,
        ]);
    }
}