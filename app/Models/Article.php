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
use App\Casts\JsonEncode;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'url', 'title', 'content', 'original_content', 'image', 'status', 'viewers', 'tags', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'url' => ArticleUrl::class,
        'tags' => JsonDecode::class,
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

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
