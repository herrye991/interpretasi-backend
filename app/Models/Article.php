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
        'uniqid', 'title', 'content', 'thumbnail', 'is_verified', 'categories', 'viewers'
    ];

    protected $casts = [
        'categories' => StringToArray::class,
        'created_at' => DateToString::class
    ];

    protected $hidden = [
        'is_verified', 'updated_at'
    ];
}
