<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    protected $keyword = "photo";
    protected $valid = "image|max:5120";

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
        $validatorMultiple = validator(
            $request->all(),
            [
                "{$this->keyword}s" => 'required',
                "{$this->keyword}s.*" => $this->valid
            ]
        );
        $validatorSingle = validator(
            $request->all(),
            [$this->keyword => "required|$this->valid"]
        );
        if ($validatorSingle->fails() && $validatorMultiple->fails()) {
            return response()->json([
                'errors' => array_merge($validatorSingle->errors()->all(), $validatorMultiple->errors()->all())
            ], 401);
        } else if ($validatorMultiple->fails()) {
            // single upload
            //save it to MinIO if no error
            $file = $request->photo->storePublicly("{$this->keyword}s");
            //add entry to the documents table on database
            $photo = $user->photos()->create([
                "path" => $file,
                "public_url" => Storage::url($file),
                "created_at" => Storage::lastModified($file),
                "updated_at" => Storage::lastModified($file),
            ]);
            return $photo;
        } else {
            // multiple upload
            $files = array();
            if ($photos = $request->file("{$this->keyword}s")) {
                foreach ($photos as $photo) {
                    //save it to MinIO if no error
                    $file = $photo->storePublicly("{$this->keyword}s");
                    $files[] = $user->photos()->create([
                        "path" => $file,
                        "public_url" => Storage::url($file),
                        "created_at" => Storage::lastModified($file),
                        "updated_at" => Storage::lastModified($file),
                    ]);
                }
            }
            return $files;
        }
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
            // deactivate photo
            $photo->is_active = false;
            $photo->save();
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
