<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'reported_user_id', 'reason', 'created_at', 'updated_at'
    ];
}
