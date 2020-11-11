<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'public_url', 'mime_type', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // On creating, generate UUID for news documents
    public static function boot()
    {
        parent::boot();
        static::creating(function ($document) {
            $document->{$document->getKeyName()} = "doc_" . Str::uuid();
        });
    }
    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
