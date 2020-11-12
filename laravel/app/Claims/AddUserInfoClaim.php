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
        if ($roles->isEmpty()) $roles = $roles->concat(["anonymous"]);
        $default_role = $roles->first();
        $token->addClaim('user', collect($user)->except(['role_id', 'provider', 'provider_id'])->put("roles", $roles));
        $token->addClaim("https://hasura.io/jwt/claims", [
            "x-hasura-allowed-roles" => $roles,
            "x-hasura-default-role" => $default_role,
            "x-hasura-user-id" => $user->id,
        ]);
        return $next($token);
    }
}
