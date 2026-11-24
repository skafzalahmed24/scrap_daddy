<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Editable title / description / tags / button of one Home screen section. */
class HomeSection extends Model
{
    protected $fillable = ['key', 'title', 'description', 'button_text', 'tags', 'is_active', 'sort'];

    protected $casts = ['tags' => 'array', 'is_active' => 'boolean'];
}
