<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Photo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path', 'public_url', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    // On creating, generate UUID for news photo
    public static function boot()
    {
        parent::boot();
        static::creating(function ($photo) {
            $photo->{$photo->getKeyName()} = "photo_" . Str::uuid();
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
