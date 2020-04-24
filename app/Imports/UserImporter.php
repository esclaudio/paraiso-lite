<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Process;
use App\Facades\Auth;
use App\Excel\Importer\ModelImporter;

class UserImporter extends ModelImporter
{
    public function getTitle(): string
    {
        return trans('Users');
    }

    public function getFields(): array
    {
        return [
            'username' => [
                'title' => trans('Username'),
                'required' => true,
            ],

            'password' => [
                'title' => trans('Password'),
                'required' => true,
            ],

            'change_password' => [
                'title' => trans('Change password'),
                'required' => false,
            ],

            'firstname' => [
                'title' => trans('Firstname'),
                'required' => true,
            ],

            'lastname' => [
                'title' => trans('Lastname'),
                'required' => false,
            ],

            'email' => [
                'title' => trans('Email'),
                'required' => false,
            ],

            'process' => [
                'title' => trans('Process'),
                'required' => true,
            ],

            'roles' => [
                'title' => trans('Roles'),
                'required' => false,
            ],

            'is_admin' => [
                'title' => trans('Is administrator?'),
                'required' => false,
            ],
        ];
    }
    
    protected function model(array $row)
    {
        if (User::where('username', $row['username'])->exists()) {
            return null;
        }

        $process = Process::where('name', $row['process'])->first();

        if ( ! $process) {
            return null;
        }

        if (isset($row['change_password']) && $row['change_password']) {
            $expirationDate = Carbon::now();
        } else {
            $expirationDate = Carbon::now()->addMonths(PASSWORD_DURATION);
        }

        $user = new User([
            'username'   => $row['username'],
            'firstname'  => $row['firstname'],
            'lastname'   => $row['lastname'] ?? null,
            'email'      => $row['email'] ?? null,
            'process_id' => $process->id,
            'is_admin'   => $row['is_admin'] ?? false,
            'is_active'  => true,
            'password_expiration_date' => $expirationDate,
            'created_by' => Auth::id()
        ]);

        $user->setPassword($row['password']);
        $user->save();

        if (isset($row['roles'])) {
            $roles = Role::whereIn('name', explode(',', $row['roles']))->get();

            if ($roles) {
                $user->roles()->sync($roles);
            }
        }

        return $user;
    }
}