<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class NotificationChannel extends Model
{
    use Notifiable;

    protected $guarded = [];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }

    public function integration()
    {
        return $this->morphTo();
    }

    public function channelType()
    {
        $spaced = str_replace('_', ' ', snake_case($this->type));
        $type = explode(' ', $spaced)[0]; // Return first word

        if ($type == 'email') {
            return 'mail';
        }

        if ($type == 'sms') {
            return 'neximo';
        }

        return $type;
    }

    public function routeNotificationForSlack()
    {
        return $this->type == 'SlackIntegration' ? $this->integration->webhook_url : null;
    }

    public function routeNotificationForMail()
    {
        return $this->type == 'EmailIntegration' ? $this->integration->email : null;
    }

    public function routeNotificationForNeximo()
    {
        return $this->type == 'SmsIntegration' ? $this->integration->sms_number : null;
    }
}
