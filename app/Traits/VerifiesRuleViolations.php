<?php

namespace App\Traits;

use App\Models\Ping;
use Illuminate\Database\Eloquent\Collection;

trait VerifiesRuleViolations
{
    // TODO: Factor in adjustments to runtime.
    public function violatesRule($rule)
    {
        // Prevent rules from firing if no pings are present. Special case for newly created monitors
        if (! $this->pings->count()) {
            return;
        }

        // TODO: Remember how to check for an instance of a class. Derp derp.
//        if (! $rule instanceof Rule::class) {
//            $var = $this->rules()->whereName($rule)->first();
//            return $this->violatesRule($var);
//        }

        $functionName = 'verify' . studly_case($rule->name) . 'Violation';

        // TODO: Check if function exists before firing.
        return $this->{$functionName}();
    }

    public function verifyCronDidNotRunViolation()
    {
        if (! $this->lastPing('run')) {
            return;
        }

        // Check for the last run endpoint
        if ($this->lastPingDate('run')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }

    public function verifyCronDidNotCompleteViolation()
    {
        if (! $this->lastPing('complete')) {
            return;
        }

        // Check for the last complete endpoint
        if ($this->lastPingDate('complete')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }

    public function verifyCronRanLongerThanUsualViolation()
    {
        $runPingPairs = $this->pings()->where('endpoint', 'run')->whereHas('pair')->with('pair')->get();
        $lastCompletePing = $this->lastPing('complete');

        // If no ping pairs are found, do not continue.
        if (! $runPingPairs || ! $lastCompletePing) {
            return;
        }

        $averageRuntime = $this->getAverageRuntimeOf($runPingPairs);
        $runtimeInQuestion = $lastCompletePing->created_at->diffInSeconds($lastCompletePing->pair->created_at);

        if (ceil($runtimeInQuestion) > ceil($averageRuntime)) {
            return true;
        }

        return false;
    }

    public function verifyIncomingHeartbeatDidNotCompleteViolation()
    {
        if (! $this->lastPing('heartbeat', 'incoming')) {
            return;
        }
        // Check for the last heartbeat endpoint
        if ($this->lastPingDate('heartbeat', 'incoming')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }

    public function getAverageRuntimeOf($pingPairs)
    {
        // TODO: Make this protected? Also, Maybe change the name to be more clear...
        $runtimeCollection = new Collection();
        foreach ($pingPairs as $ping) {
            $runtimeCollection->add($ping->created_at->diffInSeconds($ping->pair->created_at));
        }

        return $runtimeCollection->avg();
    }
}