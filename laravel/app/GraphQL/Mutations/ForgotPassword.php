<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\Events\ForgotPasswordRequested;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;

class ForgotPassword
{
    use SendsPasswordResetEmails;
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $response = $this->broker()->sendResetLink(['email' => $args['email']]);
        if ($response == Password::RESET_LINK_SENT) {
            event(new ForgotPasswordRequested($args['email']));

            return [
                'status'  => 'EMAIL_SENT',
                'message' => __($response),
            ];
        }

        return [
            'status'  => 'EMAIL_NOT_SENT',
            'message' => __($response),
        ];
    }
}
