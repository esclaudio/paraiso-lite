<?php

namespace App\Models;

use Slim\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Database\Eloquent\Model;
use App\Policies\PolicyResolver;
use App\Models\Traits\HasOperations;
use App\Models\Traits\HasHistories;
use App\Mailer\Contracts\MailableContract;
use App\Facades\Mail;
use App\Facades\Cache;

class User extends Model
{
    use HasOperations,
        HasHistories;

    const AVATARS_PATH = 'public/avatars';
    const AVATARS_URL  = 'storage/avatars';
    const EMPTY_AVATAR = '/images/empty_avatar.png';

    protected $table = 'users';
    
    protected $fillable = [
        'username',
        'name',
        'email',
        'language',
    ];
    
    // Relationships

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    // Attributes

    public function getAvatarPathAttribute()
    {
        if ($this->avatar) {
            return storage_path(sprintf('%s/%s', self::AVATARS_PATH, $this->avatar));
        }

        return null;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return sprintf('/%s/%s', self::AVATARS_URL, $this->avatar);
        }

        return self::EMPTY_AVATAR;
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmin($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeByRememberToken($query, $id, $token)
    {
        return $query->where('id', $id)->where('remember_token', $token);
    }

    // Functions

    public function setPassword(string $password)
    {
        $this->password = bcrypt($password);
        $this->remember_token = null;
    }

    public function generateRememberToken()
    {
        $this->remember_token = str_random(60);
        $this->save();
    }

    public function removeRememberToken()
    {
        $this->remember_token = null;
        $this->save();
    }

    public function can(string $ability, $model = null): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if (is_null($model)) {
            return in_array($ability, $this->getPermissions());
        }
        
        return PolicyResolver::for($model)->can($this, $ability);
    }

    public function cannot(string $ability, $model = null): bool
    {
        return ! $this->can($ability, $model);
    }

    public function getPermissions(): array
    {
        $permissions = function () {
            $roles = $this->roles->pluck('id')->toArray();

            $permissions = Permission::whereExists(
                function ($query) use ($roles) {
                    return $query->select($query->raw(1))
                        ->from('role_permission')
                        ->whereRaw('`role_permission`.`permission_id` = `permission`.`id`')
                        ->whereIn('role_permission.role_id', $roles)
                    ;
                }
            )->pluck('id')->toArray();

            return json_encode($permissions);
        };

        $key = sprintf('user.%s.permissions', $this->id);
        $json = Cache::remember($key, 60, $permissions);
        
        return json_decode($json);
    }

    public function send(MailableContract $mail)
    {
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            Mail::to($this->email, $this->name)->send($mail);
        }
    }

    public function uploadAvatar(UploadedFile $uploadedFile, ImageManager $image)
    {
        // Unlink old avatar
        if ($this->avatar) {
            $path = $this->avatar_path;

            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        $this->avatar = sprintf('%s.png', uniqid());
        
        if (storage_make(self::AVATARS_PATH)) {
            $image
                ->make($uploadedFile->file)
                ->encode('png')
                ->fit(120, 120)
                ->save($this->avatar_path);
            
            $this->save();
        }
    }

    public function deleteAvatar()
    {
        // Unlink old avatar

        if ($this->avatar) {
            $path = $this->avatar_path;

            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        $this->avatar = null;
        $this->save();
    }
}
