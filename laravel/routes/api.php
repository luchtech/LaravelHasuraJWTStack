<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
    return $request->user();
});

Route::middleware('auth:api')->get('/hasura_webhook', function (Request $request) {
    return response()->json([
        'X-Hasura-User-Id' => strval($request->user()->id),
        'X-Hasura-Role' => $request->user()->role->name,
    ]);
});

Route::middleware('auth:api')->prefix('upload')->group(function () {
    Route::post('photo', function (Request $request) {
        // $user = $request->user();
        $validator = Validator::make(
            $request->all(),
            ['photo' => 'required|image']
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $file = $request->photo->store('photos');
        return response()->json([
            "success" => true,
            "message" => "Image successfully uploaded",
            "file" => Storage::temporaryUrl($file, now()->addWeeks(1))
        ]);
    });
    Route::post('file', function (Request $request) {
        // $user = $request->user();
        $validator = Validator::make(
            $request->all(),
            ['file' => 'required|mimes:doc,docx,pdf,txt|max:2048']
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $file = $request->file->store('files');
        return response()->json([
            "success" => true,
            "message" => "File successfully uploaded",
            "file" => Storage::temporaryUrl($file, now()->addWeeks(1))
        ]);
    });
});
