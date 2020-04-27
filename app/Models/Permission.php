<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    
    public $timestamps = false;
    
    public $incrementing = false;

    public static function flatten(Role $role = null): array
    {
        $rolePermissions = [];

        if ($role) {
            $rolePermissions = $role->permissions()
                ->orderBy('permission_id')
                ->pluck('permission_id')
                ->toArray();
        }

        $permissions = [];

        foreach (Permission::orderBy('id')->get() as $permission) {
            $keys = explode('.', $permission->id);
            $parent = array_shift($keys);

            if (count($keys) === 0) {
                // Parent
                $permissions[$parent] = [
                    'id'          => $permission->id,
                    'description' => $permission->description,
                    'options'     => []
                ];
            } else {
                // Option
                if (array_key_exists($parent, $permissions)) {
                    $permissions[$parent]['options'][] = [
                        'id'          => $permission->id,
                        'description' => $permission->description,
                        'selected'    => in_array($permission->id, $rolePermissions),
                    ];
                }
            }
        }

        return $permissions;
    }
}
