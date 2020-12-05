<?php

namespace App\GraphQL\Mutations;

use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use Joselfonseca\LighthouseGraphQLPassport\Events\UserRefreshedToken;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Parser;

class RefreshToken extends BaseAuthResolver
{
    /**
     * @param  null  $_
     * @param  array<string, mixed>  $args
     */
    public function __invoke($_, array $args)
    {
        $credentials = $this->buildCredentials($args, 'refresh_token');

        $response = $this->makeRequest($credentials);

        // let's get the user id from the new Access token so we can emit an event
        $userId = $this->parseToken($response['access_token']);

        $model = $this->makeAuthModelInstance();

        $user = $model->findOrFail($userId);

        event(new UserRefreshedToken($user));

        return $response;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function makeAuthModelInstance()
    {
        return $this->getAuthModelFactory()->make();
    }

    /**
     * @param $accessToken
     *
     * @return false|mixed
     */
    public function parseToken($accessToken)
    {
        $key_path = Passport::keyPath('oauth-public.key');
        $parseTokenKey = file_get_contents($key_path);

        $token = (new Parser())->parse((string) $accessToken);

        $signer = new Sha256();

        if ($token->verify($signer, $parseTokenKey)) {
            $userId = $token->getClaim('sub');

            return $userId;
        } else {
            return false;
        }
    }
}
