<?php

namespace App\Http\Controllers\API;

use App\Claims\AddUserInfoClaim;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

class MeController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:api");
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = $this->getRoles($user);
        return response()->json([
            "id" => $user->id,
            "roles" => $roles
        ]);
    }

    /**
     * Get Roles Array from User or Anonymous
     */

    public static function getRoles(User $user)
    {
        $roles = $user->users_roles()->where(["is_approved" => true, "is_active" => true])->get()
            ->map(function ($user_role) {
                return $user_role->role;
            })
            ->sortBy('access_level')
            ->pluck('name');
        if ($roles->isEmpty()) $roles = $roles->concat(["anonymous"]);
        return $roles;
    }
}
