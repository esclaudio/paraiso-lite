<?php

namespace App\Datatable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Customer;

class CustomerDatatable extends Datatable
{
    protected function query(): Builder
    {
        return Customer::query();
    }

    protected function order(Builder $query): void
    {
        $query->orderBy('code');
    }

    protected function filter(Builder $query): void
    {
        if ($search = $this->request->getParam('search')) {
            $query->where(function ($query) use ($search) {
                $query->where('code', 'like', $search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            });
        }
    }

    protected function transform(Model $model): array
    {
        return [
            'id'   => $model->id,
            'code' => $model->code,
            'name' => $model->name,
            // 'edit_url' => $router->pathFor('customers.edit', ['customer' => $model->id]),
        ];
    }
}