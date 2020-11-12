<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use \App\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->post('/user', function (Request $request) {
    $roles = collect($request->user()->users_roles)->map(function ($user_role) {
        return $user_role->role->name;
    });
    return response()->json([
        "id" => $request->user()->id,
        "roles" => $roles
    ]);
});

Route::middleware('auth:api')->get('/hasura_webhook', function (Request $request) {
    return response()->json([
        'X-Hasura-User-Id' => strval($request->user()->id),
        'X-Hasura-Role' => $request->user()->role->name,
    ]);
});

Route::post('/verifyGoogle', function (Request $request) {
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
});

//Routes for Uploads to MinIO
Route::middleware('auth:api')->prefix('upload')->group(function () {
    //Photo Upload
    Route::post('photo', function (Request $request) {
        $user = $request->user();
        $validator = Validator::make(
            $request->all(),
            ['photo' => 'required|image|max:5120']
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //save it to MinIO if no error
        $file = Storage::put("photos", $request->photo, 'public');
        //add entry to the photos table on database
        $photo = $user->photos()->create([
            "path" => $file,
            "public_url" => Storage::url($file),
            "created_at" => Storage::lastModified($file),
            "updated_at" => Storage::lastModified($file),
        ]);
        return response()->json($photo);
    });
    //File Upload
    Route::post('file', function (Request $request) {
        $user = $request->user();
        $validator = Validator::make(
            $request->all(),
            ['file' => 'required|mimes:doc,docx,pdf,txt|max:5120']
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //save it to MinIO if no error
        $file = Storage::put("files", $request->file, 'public');
        //add entry to the documents table on database
        $document = $user->documents()->create([
            "path" => $file,
            "public_url" => Storage::url($file),
            "mime_type" => Storage::mimeType($file),
            "created_at" => Storage::lastModified($file),
            "updated_at" => Storage::lastModified($file),
        ]);
        return response()->json($document);
    });
});
