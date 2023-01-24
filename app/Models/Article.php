<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\DateToString;
use App\Casts\StringToArray;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'content', 'thumbnail', 'is_verified', 'categories', 'viewers', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'categories' => StringToArray::class,
        'created_at' => DateToString::class
    ];

    protected $hidden = [
        'is_verified', 'updated_at'
    ];

    function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    function likes()
    {
        return $this->hasMany('App\Models\Like');
    }
}
