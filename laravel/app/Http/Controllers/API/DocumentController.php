<?php

namespace App\Http\Controllers\API;

use App\Document;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{

    protected $keyword = "document";
    protected $valid = "mimes:doc,docx,pdf,txt|max:5120";
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
        return request()->user()->documents;
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
            $file = $request->file($this->keyword)->storePublicly("{$this->keyword}s");
            //add entry to the documents table on database
            $photo = $user->documents()->create([
                "path" => $file,
                "public_url" => Storage::url($file),
                "mime_type" => Storage::mimeType($file),
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
                    $files[] = $user->documents()->create([
                        "path" => $file,
                        "public_url" => Storage::url($file),
                        "mime_type" => Storage::mimeType($file),
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
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        // check first if he is the owner of the photo
        if (!request()->user()->documents->contains($document)) {
            abort(403, "Unauthorized.");
        }

        // check if document still exists
        if (!Storage::exists($document->path)) {
            // deactivate document
            $document->is_active = false;
            $document->save();
            abort(404, "Document not found.");
        }

        // delete from MinIO and remove trace
        Storage::delete($document->path);
        $document->path = "";
        $document->public_url = "";
        $document->mime_type = "";

        // deactivate document
        $document->is_active = false;
        $document->save();

        return response()->json([
            "success" => !Storage::exists($document->path) ? "true" : "false"
        ]);
    }
}
