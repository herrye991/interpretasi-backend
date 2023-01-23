<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id', 'article_id', 'created_at', 'updated_at'
    ];

    protected $hidden = [
        'user_id', 'updated_at'
    ];
}
