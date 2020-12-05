<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use Joselfonseca\LighthouseGraphQLPassport\Events\UserLoggedIn;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Login extends BaseAuthResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $credentials = $this->buildCredentials($args);
        $response = $this->makeRequest($credentials);
        $user = $this->findUser($args['username']);

        $this->validateUser($user);

        event(new UserLoggedIn($user));

        return array_merge(
            $response,
            [
                'user' => $user,
            ]
        );
    }

    protected function validateUser($user)
    {
        $authModelClass = $this->getAuthModelClass();
        if ($user instanceof $authModelClass && $user->exists) {
            return;
        }

        throw (new ModelNotFoundException())
            ->setModel($authModelClass);
    }

    protected function getAuthModelClass(): string
    {
        return $this->getAuthModelFactory()->getClass();
    }

    protected function makeAuthModelInstance()
    {
        return $this->getAuthModelFactory()->make();
    }

    protected function findUser(string $username)
    {
        $model = $this->makeAuthModelInstance();

        if (method_exists($model, 'findForPassport')) {
            return $model->findForPassport($username);
        }

        return $model::query()
            ->where(config('lighthouse-graphql-passport.username'), $username)
            ->first();
    }
}
