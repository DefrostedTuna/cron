<?php

namespace App\Traits;

use App\Models\NotificationChannel;

trait CreatesNotificationChannels
{
    public function createNotificationChannelFromIntegration($integration)
    {
        return NotificationChannel::create([
            'monitor_id' => $this->id,
            'integration_id' => $integration->id,
            'integration_type' => get_class($integration),
            'type' => class_basename($integration),
        ]);
    }
}