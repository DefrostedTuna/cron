<?php

namespace App\Models;

use App\Traits\RecordsMonitorActivity;
use Illuminate\Database\Eloquent\Model;

class Ping extends Model
{
    protected $guarded = [];

    public function monitor()
    {
        return $this->belongsTo(\App\Models\Monitor::class);
    }
}