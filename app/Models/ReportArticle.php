<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reported_article_id', 'reason', 'created_at', 'updated_at'
    ];
}
