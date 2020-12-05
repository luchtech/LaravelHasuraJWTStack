<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\AuthenticationException;
use Joselfonseca\LighthouseGraphQLPassport\Events\UserLoggedOut;
use Illuminate\Support\Facades\Auth;

class Logout extends BaseAuthResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        if (!Auth::guard('api')->check()) {
            throw new AuthenticationException('Not Authenticated', 'Not Authenticated');
        }
        $user = Auth::guard('api')->user();
        // revoke user's token
        Auth::guard('api')->user()->token()->revoke();

        event(new UserLoggedOut($user));

        return [
            'status'  => 'TOKEN_REVOKED',
            'message' => __('Your session has been terminated'),
        ];
    }
}
