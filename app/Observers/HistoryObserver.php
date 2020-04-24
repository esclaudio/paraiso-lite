<?php

namespace App\Observers;

use App\Models\History;
use App\Facades\Request;
use App\Facades\Auth;

class HistoryObserver
{
    /**
    * Listen to the Model created event.
    *
    * @param  mixed $model
    * @return void
    */
    public function created($model)
    {   
        $metadata = [];
        
        foreach ($model->getAttributes() as $attribute => $value) {
            $metadata[$attribute] = ['old' => null, 'new' => $value];
        }

        $model->morphMany(History::class, 'model')->create([
            'event'      => 'created',
            'metadata'   => $metadata,
            'user_id'    => static::getUserId(),
            'ip_address' => static::getIp(),
        ]);
    }

    /**
    * Listen to the Model updating event.
    *
    * @param  mixed $model
    * @return void
    */
    public function updating($model)
    {
        /*
        * Gets the model's altered values and tracks what had changed
        */
        
        $metadata = [];

        foreach ($model->getDirty() as $attribute => $value) {
            $metadata[$attribute] = [
                'old' => $model->original[$attribute] ?? null,
                'new' => $value,
            ];
        }

        $model->morphMany(History::class, 'model')->create([
            'event'      => 'updated',
            'metadata'   => $metadata,
            'user_id'    => static::getUserId(),
            'ip_address' => static::getIp(),
        ]);
    }

    /**
    * Listen to the Model deleting event.
    *
    * @param  mixed $model
    * @return void
    */
    public function deleting($model)
    {
        $metadata = [];

        foreach ($model->getAttributes() as $attribute => $value) {
            $metadata[$attribute] = ['old' => $value, 'new' => null];
        }

        $model->morphMany(History::class, 'model')->create([
            'event'      => 'deleted',
            'metadata'   => $metadata,
            'user_id'    => static::getUserId(),
            'ip_address' => static::getIp(),
        ]);
    }

    protected static function getUserId()
    {
        return Auth::user()->id ?? null;
    }

    protected static function getIp()
    {
        return Request::getServerParam('REMOTE_ADDR');
    }
}