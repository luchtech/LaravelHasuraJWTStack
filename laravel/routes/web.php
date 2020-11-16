<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;
// use \App\User;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/login/google', function (Request $request) {
//     return Socialite::driver('google')->redirect();
// });

// Route::get('/login/google/callback', function (Request $request) {
//     // dd($request);
//     //default
//     // $login = Socialite::driver('google')->user();
//     // $userData = Socialite::driver('google')->userFromToken($login->token);

//     //working web google sign in
//     // $userData = Socialite::driver('google')->userFromToken("ya29.a0AfH6SMD4ROjPtCGLNb43OFfpenHzorUtjR65eRAMAElaR8Cf9WXgWc3rXR28TnWF87BM4i0kt7p8emJ9eo9eP_e86mlhkVzs6pAxd4NLBaFdBb64yVQfBc0Gwa-y_vQTD-99r6w1N0azDKZCVPz3Xvs015lo5LkRSA0");

//     //attempts on android sign-in
//     // $serverAuthCode = "4/5AH_QgyPl51fciwl9_15Q80pE1J7WgrK0f3CtRDfEBeyMGV1fp2qMqd0hfrfq7_REWtMLjflPPO8qTlbPZTcIsk";
//     $serverAuthCode = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjdkYTc4NjNlODYzN2Q2NjliYzJhMTI2MjJjZWRlMmE4ODEzZDExYjEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI2MDc2MjY1MjM5MjAtaDNrbGlrcDZvOHU0YXNoM201dGU5YjRoZnYxNjBva2guYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI2MDc2MjY1MjM5MjAtdXQ3a2o4MTdwY290YWlyZm1oaDF0bjloYzhxNXVlM2QuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDM1OTg1NDMxMjQ3MDE2MzQ2MDMiLCJlbWFpbCI6ImV6cmlkZWFwcEBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwibmFtZSI6IkV6IFJpZGUiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDQuZ29vZ2xldXNlcmNvbnRlbnQuY29tLy1EdjF2VXVSNzFaNC9BQUFBQUFBQUFBSS9BQUFBQUFBQUFBQS9BTVp1dWNsTVdPaTlfRV84aS03UzZ1dEpVR3ZXOEhkZnRBL3M5Ni1jL3Bob3RvLmpwZyIsImdpdmVuX25hbWUiOiJFeiIsImZhbWlseV9uYW1lIjoiUmlkZSIsImxvY2FsZSI6ImVuIiwiaWF0IjoxNjAyMjU5NTg0LCJleHAiOjE2MDIyNjMxODR9.f0WszpgtyIiv3G3VO5DEk0XeAwrWjw6f1OseHaLQMagkQIiIrxZh5SEr3Jsdeh4gIKbPiyhOHy_ByNhqau2jucTVszV_zm8hWVUWkOkBrsvFwIXtl_GA1Pyqc3ffqu7VFhHXjNlinpuyxueIfS3keCRr84GTDwd-vzn1heIRE5NzEMCYzUZkAN-SjjdNnMAP2F2t6MoDOv883wAjNx0NG7T1VvIQvHLni2lrHU4S34n7QtpphYTtnGOCaXLyfinqgcyy5bTpL_sWujH7FbExlzFzBUSYE9lK8BeebYB_fbVmysrNY5sUJcbfsUsl4a6m63c01eQut8z4R6FBvY2jcQ";
//     $provider = "google";
//     $driver = Socialite::driver($provider); // provider is 'google' here
//     // $access_token = $driver->stateless()->getAccessToken($serverAuthCode);
//     $userData = $driver->userFromToken($serverAuthCode);
//     dd($userData);
//     //     try {
//     //         $user = User::where('email', $userData->email)->firstOrFail();
//     //     } catch (ModelNotFoundException $e) {
//     //         $user = User::create([
//     //             'given_name'        => $userData->user['given_name'],
//     //             'middle_name'       => "",
//     //             'surname'           => $userData->user['family_name'],
//     //             'email'             => $userData->user['email'],
//     //             'provider'          => "google",
//     //             'provider_id'       => $userData->token,
//     //             'password'          => Hash::make(Str::random(16)),
//     //             'avatar'            => $userData->avatar,
//     //             'email_verified_at' => now(),
//     //             'role_id'           => 1
//     //         ]);
//     //     }
//     //     Auth::onceUsingId($user->id);
//     //     return $user;
// });
