<?php

namespace App\Traits;

use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

trait NotifiableChildren
{
    public function notifyChildren($notification)
    {
        $target = isset($this->childrenToNotify) ? $this->childrenToNotify : 'notificationChannels';

        Notification::send($this->{$target}, $notification);

        DatabaseNotification::create([
            'id' => Uuid::generate()->string,
            'type' => get_class($notification),
            'notifiable_id' => $this->id,
            'notifiable_type' => get_class($this),
            'data' => $notification->toParentDatabase(),
        ]);

        $this->delay_until = \Carbon\Carbon::now()->addMinutes(6);
        $this->save();
    }
}