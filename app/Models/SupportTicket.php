<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** "Raise a ticket" from the website / mobile app. Answered from the admin panel. */
class SupportTicket extends Model
{
    public const STATUSES = ['open', 'in_progress', 'closed'];

    protected $fillable = ['user_uuid', 'subject', 'message', 'status', 'admin_reply', 'replied_at'];

    protected $casts = ['replied_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }
}
