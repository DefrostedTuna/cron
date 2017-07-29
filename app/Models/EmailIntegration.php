<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailIntegration extends Model
{
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function notificationChannels()
    {
        return $this->morphMany(NotificationChannel::class, 'integration');
    }
}
