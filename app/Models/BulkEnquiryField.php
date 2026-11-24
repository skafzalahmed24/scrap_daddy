<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One question of the bulk enquiry form, configured by the admin. */
class BulkEnquiryField extends Model
{
    public const TYPES = ['text' => 'Text', 'textarea' => 'Long text', 'number' => 'Number', 'phone' => 'Phone', 'email' => 'Email', 'dropdown' => 'Dropdown'];

    protected $fillable = ['label', 'type', 'options', 'required', 'is_active', 'sort'];

    protected $casts = ['options' => 'array', 'required' => 'boolean', 'is_active' => 'boolean'];
}
