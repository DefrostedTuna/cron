<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlackIntegration extends Model
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

    // TODO: Don't need to do this. Just reference it when I need it
//    public function monitors()
//    {
//        return $this->notificationChannels()->with('monitor')->get();
//    }
}
