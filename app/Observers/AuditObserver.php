<?php

namespace App\Observers;

use App\Facades\Auth;

class AuditObserver
{
    /**
    * Listen to the Model creating event.
    *
    * @param  mixed $model
    * @return void
    */
    public function creating($model)
    {   
        $model->created_by = Auth::user()->id;
    }

    /**
    * Listen to the Model updating event.
    *
    * @param  mixed $model
    * @return void
    */
    public function updating($model)
    {
        $model->updated_by = Auth::user()->id;
    }
}