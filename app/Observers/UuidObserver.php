<?php

namespace App\Observers;

use Ramsey\Uuid\Uuid;

class UuidObserver
{
    /**
    * Listen to the Model creating event.
    *
    * @param  mixed $model
    * @return void
    */
    public function creating($model)
    {
        if ( ! $model->id) {
            $model->{$model->getKeyName()} = Uuid::uuid4()->toString();
        }
    }
}