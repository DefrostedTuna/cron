<?php

namespace App\Models;

use Carbon\Carbon;
use Cron\CronExpression;
use App\Traits\NotifiableChildren;
use App\Queries\CommonMonitorQueries;
use App\Traits\VerifiesRuleViolations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Traits\CreatesNotificationChannels;

class Monitor extends Model
{
    use Notifiable,
        NotifiableChildren,
        CreatesNotificationChannels,
        VerifiesRuleViolations,
        CommonMonitorQueries;

    protected $guarded = [];

    /**
     * Relationships
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function pings()
    {
        return $this->hasMany(Ping::class);
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class);
    }

    public function notificationChannels()
    {
        return $this->hasMany(NotificationChannel::class);
    }

    /**
     * Methods
     */

    public function isDue()
    {
        return $this->expressionInstance()->isDue();
    }

    public function expressionInstance()
    {
        return CronExpression::factory($this->attributes['expression']);
    }

    public function lastPing($endpoint = null, $type = null)
    {
        $ping = $this->pings();

        if ($endpoint) {
            $ping->where('endpoint', $endpoint);
        }

        if ($type) {
            $ping->where('type', $type);
        }

        return $ping->orderBy('created_at', 'desc')->first();
    }
    
    public function lastPingDate($endpoint = null, $type = null)
    {
        return $this->lastPing($endpoint, $type)->created_at;
    }

    // Move this to a rules class?
    public function lastExpectedEndpoint()
    {
        if ($this->type == 'cron') {
            return $this->lastPing()->endpoint == 'run' ? 'complete' : 'run';
        }
    }

    public function lastExpectedRunDate()
    {
        // TODO: Place adjusted run date here with a toggle param?
        return Carbon::instance($this->expressionInstance()->getPreviousRunDate());
    }

    public function lastExpectedRunDateAdjusted()
    {
        // TODO: Calculate the adjust date here
        return Carbon::instance($this->expressionInstance()->getPreviousRunDate());
    }

    public function nextExpectedRunDate()
    {
        // TODO: Place adjusted run date here with a toggle param?
        return Carbon::instance($this->expressionInstance()->getNextRunDate());
    }

    public function nextExpectedRunDateAdjusted()
    {
        // TODO: Calculate the adjust date here
        return Carbon::instance($this->expressionInstance()->getNextRunDate());
    }
}
