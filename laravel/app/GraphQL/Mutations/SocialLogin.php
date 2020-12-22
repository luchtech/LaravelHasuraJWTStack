<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use Illuminate\Support\Facades\Auth;

class SocialLogin extends BaseAuthResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $credentials = $this->buildCredentials($args, 'social_grant');
        $response = $this->makeRequest($credentials);
        $model = app(config('auth.providers.users.model'));
        $user = $model->where('id', Auth::user()->id)->firstOrFail();
        $response['user'] = $user;

        return $response;
    }
}
