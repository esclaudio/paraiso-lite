<?php

namespace App\Support\Excel\Importer;

abstract class ModelImporter extends Importer
{
    /**
     * Model
     *
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    abstract protected function model(array $row);


    protected function save($row): bool
    {
        if ($model = $this->model($row)) {
            try {
                $model->save();
                return true;
            } catch (\Exception $e) {
                $this->errors[] = $row;
            }
        } else {
            $this->errors[] = $row;
        }
        
        return false;
    }
}