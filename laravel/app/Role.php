<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public function users_roles()
    {
        return $this->hasMany('App\UsersRoles');
    }
}
