<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Joselfonseca\LighthouseGraphQLPassport\HasSocialLogin;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasSocialLogin;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'given_name', 'middle_name', 'surname', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function users_roles()
    {
        return $this->hasMany('App\UsersRoles');
    }

    public function photos()
    {
        return $this->hasMany('App\Photo');
    }

    public function documents()
    {
        return $this->hasMany('App\Document');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public static function byOAuthToken(Request $request)
    {
        $userData = Socialite::driver($request->get('provider'))->userFromToken($request->get('token'));
        try {
            $user = static::where('email', $userData->email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = static::create([
                'email'             => $userData->email,
                'password'          => Hash::make(Str::random(16)),
                'provider'          => $request->get('provider'),
                'provider_id'       => $userData->token,
                'email_verified_at' => $request->get('token'),
                'given_name'        => $userData->user['given_name'],
                'middle_name'       => "",
                'surname'           => $userData->user['family_name'],
            ]);
            $user_role = new UsersRoles();
            $user_role->role_id = 1;
            $user->users_roles()->save($user_role);
        }
        Auth::onceUsingId($user->id);
        return $user;
    }

    // On creating, generate UUID for news users
    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->{$user->getKeyName()} = "user_" . Str::uuid();
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
