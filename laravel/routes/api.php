<?php

use Illuminate\Support\Facades\Route;

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

Route::post("me", "MeController");
Route::get("hasura_webhook", "HasuraWebhookController");
Route::post("verify_google", "VerifyGoogleController");
Route::apiResource("photos", "PhotoController")->except(["show", "update"]);
Route::apiResource("documents", "DocumentController")->except(["show", "update"]);
