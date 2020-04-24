<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;
use Carbon\Carbon;
use App\Models\DocumentStatus;

class DocumentFilter extends Filter
{
    protected function applyCode(string $value)
    {
        $this->query->where('code', 'like', "{$value}%");
    }

    protected function applyTitle(string $value)
    {
        $this->query->where('title', 'like', "%{$value}%");
    }

    protected function applySystemId(string $value)
    {
        $this->query->where('system_id', (int)$value);
    }

    protected function applyProcessId(string $value)
    {
        $this->query->where('process_id', (int)$value);
    }

    protected function applyDocumentTypeId(string $value)
    {
        $this->query->where('document_type_id', (int)$value);
    }

    protected function applyResponsibleId(string $value)
    {
        $this->query->where('responsible_id', (int)$value);
    }

    protected function applyOverdueEffectiveDate(string $value)
    {
        if ($value === '1') {
            $this->related('versions', function (Builder $query) {
                $query->where('status', DocumentStatus::PUBLISHED)
                    ->where('next_periodic_review_date', '<=', Carbon::today());
            });
        }
    }
}