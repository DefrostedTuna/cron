<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsIntegration extends Model
{
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function notificationChannels()
    {
        return $this->morphMany(\App\Models\NotificationChannel::class, 'integration');
    }
}
