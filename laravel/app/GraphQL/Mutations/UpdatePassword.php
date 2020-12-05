<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Events\PasswordUpdated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UpdatePassword
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $guard = Auth::guard();
        $user = $guard->user();
        if (!Hash::check($args['old_password'], $user->password)) {
            throw new ValidationException([
                'password' => __('Current password is incorrect'),
            ], 'Validation Exception');
        }
        $user->password = Hash::make($args['password']);
        $user->save();
        event(new PasswordUpdated($user));

        return [
            'status'  => 'PASSWORD_UPDATED',
            'message' => __('Your password has been updated'),
        ];
    }
}
