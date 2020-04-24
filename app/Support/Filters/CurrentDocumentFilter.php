<?php

namespace App\Filters;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use DateTime;
use Carbon\Carbon;
use App\Models\DocumentStatus;

class CurrentDocumentFilter extends Filter
{
    protected function setup()
    {
        $onlyNew = (bool)$this->value('only_new', false);

        $this->query->whereHas('versions', function ($query) use ($onlyNew) {
            $query->published();

            if ($onlyNew) {
                $query
                    ->whereDate('published_at', '<=', Carbon::now()->addDays(DOCUMENT_NEW_DAYS))
                    ->whereDate('published_at', '>=', Carbon::now()->subDays(DOCUMENT_NEW_DAYS));
            }
        });
    }

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
}