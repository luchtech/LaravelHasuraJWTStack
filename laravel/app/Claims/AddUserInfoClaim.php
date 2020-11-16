<?php

namespace App\Claims;

use App\User;
use App\Http\Controllers\API\MeController;

class AddUserInfoClaim
{
    public function handle($token, $next)
    {
        $user = User::find($token->getUserIdentifier());
        $roles = MeController::getRoles($user);
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
