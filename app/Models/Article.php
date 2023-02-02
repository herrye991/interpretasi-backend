<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\DateToString;
use App\Casts\StringToArray;
use App\Casts\ArticleUrl;
use App\Casts\Categories;
use App\Casts\ImageUrl;
use App\Casts\JsonDecode;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'url', 'title', 'content', 'image', 'status', 'categories', 'viewers', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'url' => ArticleUrl::class,
        'categories' => Categories::class,
        'created_at' => DateToString::class
    ];

    protected $hidden = [
        'user_id', 'status', 'updated_at'
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
