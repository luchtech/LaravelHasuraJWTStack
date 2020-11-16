<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return request()->user()->photos;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        // $photos = $request->validate([
        //     "photos" => "required",
        //     "photos.*" => "image"
        // ]);
        $validator = validator(
            $request->all(),
            [
                "photo" => "required|image|max:5120",
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //save it to MinIO if no error
        $file = $request->photo->storePublicly("photos");
        //add entry to the photos table on database
        $photo = $user->photos()->create([
            "path" => $file,
            "public_url" => Storage::url($file),
            "created_at" => Storage::lastModified($file),
            "updated_at" => Storage::lastModified($file),
        ]);
        return $photo;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function show(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Photo  $photo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Photo $photo)
    {
        // check first if he is the owner of the photo
        if (!request()->user()->photos->contains($photo)) {
            abort(403, "Unauthorized.");
        }

        // check if photo still exists
        if (!Storage::exists($photo->path)) {
            abort(404, "Photo not found.");
        }

        // delete from MinIO and remove trace
        Storage::delete($photo->path);
        $photo->path = "";
        $photo->public_url = "";

        // deactivate photo
        $photo->is_active = false;
        $photo->save();

        return response()->json([
            "success" => !Storage::exists($photo->path) ? "true" : "false"
        ]);
    }
}
