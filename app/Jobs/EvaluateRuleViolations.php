<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EvaluateRuleViolations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $monitor;

    /**
     * Create a new job instance.
     *
     * @param $monitor
     */
    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->monitor->rules as $rule) {
            if ($this->monitor->violatesRule($rule)) {
                $brokenRuleNotification = "\\App\\Notifications\\" . studly_case($rule->name);
                $this->monitor->notifyChildren(new $brokenRuleNotification($this->monitor));
                // TODO: Also broadcast event? Yeah, that'd be cool
                break; // Prevent sending of multiple notifications
            }
        }
    }
}
