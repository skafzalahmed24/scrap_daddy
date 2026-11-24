<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkEnquiry extends Model
{
    protected $fillable = ['user_uuid', 'name', 'phone', 'answers', 'status', 'admin_note'];

    protected $casts = ['answers' => 'array'];
}
