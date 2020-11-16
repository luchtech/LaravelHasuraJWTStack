<?php

namespace App\Http\Controllers\API;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;

class VerifyGoogleController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $userData = Socialite::driver($request->get('provider'))->userFromToken($request->get('token'));
        try {
            $user = User::where('provider', Str::lower($request->get('provider')))->where('provider_id', $userData->getId())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $user = User::create([
                'given_name'        => $userData->get('given_name'),
                'middle_name'       => "",
                'surname'           => $userData->get('family_name'),
                'email'             => $userData->getEmail(),
                'provider'          => $request->get('provider'),
                'provider_id'       => $userData->getId(),
                'password'          => Hash::make(Str::random(16)),
                'avatar'            => $userData->getAvatar(),
                'email_verified_at' => now(),
                'role_id'           => 1
            ]);
        }
        Auth::onceUsingId($user->id);
        return $user;
    }
}
