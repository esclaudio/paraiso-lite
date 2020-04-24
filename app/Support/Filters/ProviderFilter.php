<?php

namespace App\Filters;

class ProviderFilter extends Filter
{
    protected function applyCode(string $value)
    {
        $this->query->where('code', $value);
    }

    protected function applyName(string $value)
    {
        $this->query->where('name', 'like', "%{$value}%");
    }

    protected function applyProviderTypeId(string $value)
    {
        $this->query->where('provider_type_id', $value);
    }

    protected function applyProviderClassificationId(string $value)
    {
        $this->query->where('provider_classification_id', $value);
    }
}