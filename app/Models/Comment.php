<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'article_id', 'body', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'user_id', 'article_id', 'updated_at'
    ];

    function user ()
    {
        return $this->belongsTo('App\Models\User');
    }
}
