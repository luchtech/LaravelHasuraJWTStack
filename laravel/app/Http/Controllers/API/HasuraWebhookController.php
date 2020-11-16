<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HasuraWebhookController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware("auth:api");
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        $user = $request->user();
        $roles = MeController::getRoles($user);
        return response()->json([
            'X-Hasura-User-Id' => strval($user->id),
            'X-Hasura-Role' => $roles->first(),
        ]);
    }
}
