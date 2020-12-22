<?php

namespace App\GraphQL\Mutations;

use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException;

class VerifyEmail
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $decodedToken = json_decode(base64_decode($args['token']));
        $expiration = decrypt($decodedToken->expiration);
        $email = decrypt($decodedToken->hash);
        if (Carbon::parse($expiration) < now()) {
            throw new ValidationException([
                'token' => __('The token is invalid'),
            ], 'Validation Error');
        }
        $model = app(config('auth.providers.users.model'));

        try {
            $user = $model->where('email', $email)->firstOrFail();
            $user->markEmailAsVerified();
            event(new Verified($user));
            Auth::onceUsingId($user->id);
            $tokens = $user->getTokens();
            $tokens['user'] = $user;

            return $tokens;
        } catch (ModelNotFoundException $e) {
            throw new ValidationException([
                'token' => __('The token is invalid'),
            ], 'Validation Error');
        }
    }
}
