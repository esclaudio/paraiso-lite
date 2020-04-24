<?php

namespace App\Support\Excel\Importer;

abstract class Importer
{
    /**
     * Mapping
     *
     * @var array
     */
    protected $mapping = [];

    /**
     * Count
     * 
     * @var int
     */
    protected $count = 0;

    /**
     * Errors
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Get title
     *
     * @return string
     */
    abstract protected function getTitle(): string;

    /**
     * Get fields
     *
     * @return array
     */
    abstract protected function getFields(): array;
    
    /**
     * Save
     *
     * @param mixed $row
     * 
     * @return void
     */
    abstract protected function save($row): bool;

    /**
     * Import
     *
     * @param array $rows
     * 
     * @return void
     */
    public function import(array $rows)
    {
        $this->count = 0;
        $this->errors = [];

        $required = $this->getRequiredFields();

        foreach ($rows as $row) {
            $row = $this->map($row);
            
            if ($this->validate($row, $required) && $this->save($row)) {
                $this->count++;
            }
        }
    }
    
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Count
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Map
     *
     * @param array $row
     * 
     * @return array
     */
    protected function map(array $row): array
    {
        $mapped = [];

        foreach ($this->mapping as $field => $heading) {
            $mapped[$field] = $row[$heading] ?? null;
        }

        return $this->emptyToNull($mapped ?: $row);
    }

    /**
     * Empty to null
     *
     * @param array $row
     * 
     * @return array
     */
    protected function emptyToNull(array $row): array
    {
        $clean = [];

        foreach ($row as $field => $heading) {
            $value = trim($row[$field]);
            $clean[$field] = $value === ''? null: $value;
        }

        return $clean;
    }

    /**
     * Validate
     *
     * @param array $row
     * @param array $required
     * 
     * @return boolean
     */
    protected function validate(array $row, array $required): bool
    {
        foreach ($required as $field) {
            if ( ! isset($row[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get required fields
     *
     * @return array
     */
    protected function getRequiredFields(): array
    {
        $required = [];

        foreach ($this->getFields() as $field => $options) {
            if (isset($options['required']) && $options['required']) {
                $required[] = $field;
            }
        }

        return $required;
    }
}