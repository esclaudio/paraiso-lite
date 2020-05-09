<?php

namespace App\Filters\Fields;

use Slim\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Filters\Fields\Base\CheckField;

class DocumentOnlyNewField extends CheckField
{
    const DAYS_LIMIT = 7;

    public function __construct()
    {
        parent::__construct('only_new', trans('Only New'));
    }

    public function apply(Request $request, Builder $query, string $value): void
    {
        if ('1' === $value) {
            $query->whereHas('versions', function (Builder $subquery) {
                $subquery
                    ->whereDate('approved_at', '<=', Carbon::now()->addDays(self::DAYS_LIMIT))
                    ->whereDate('approved_at', '>=', Carbon::now()->subDays(self::DAYS_LIMIT));
            });
        }
    }
}