<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{

    /*
     * @var array
     */
    protected $fillable = [
        'name', 'access_level'
    ];

    public function users_roles()
    {
        return $this->hasMany('App\UsersRoles');
    }

    // On creating, generate UUID for news roles for user
    public static function boot()
    {
        parent::boot();
        static::creating(function ($role) {
            $role->{$role->getKeyName()} = 'role_' . Str::uuid();
        });
    }
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
