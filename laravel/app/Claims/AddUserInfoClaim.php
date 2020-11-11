<?php

namespace App\Claims;

class AddUserInfoClaim
{
    public function handle($token, $next)
    {
        $user = \App\User::find($token->getUserIdentifier());
        $roles = $user->users_roles()->where(["is_approved" => true, "is_active" => true])->get()
            ->map(function ($user_role) {
                return $user_role->role;
            })
            ->sortBy('access_level')
            ->pluck('name');
        $default_role = $roles->first();
        $token->addClaim('user', [
            "id" => $user->id,
            "email" => $user->email,
            "given_name" => $user->given_name,
            "middle_name" => $user->middle_name,
            "surname" => $user->surname,
            "avatar" => $user->avatar,
            "phone_number" => $user->phone_number,
            "roles" => $roles
        ]);
        $token->addClaim("https://hasura.io/jwt/claims", [
            "x-hasura-allowed-roles" => $roles,
            "x-hasura-default-role" => $default_role,
            "x-hasura-user-id" => strval($user->id),
        ]);
        return $next($token);
    }
}
