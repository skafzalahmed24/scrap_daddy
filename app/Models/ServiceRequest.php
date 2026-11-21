<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * "Service not available in my area" enquiry submitted from the pickup form (web and app).
 */
class ServiceRequest extends Model
{
    protected $fillable = [
        'user_uuid', 'name', 'phone', 'email', 'pin_code', 'area', 'message', 'status', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public const STATUSES = ['new', 'contacted', 'closed'];
}
