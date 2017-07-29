<?php

namespace App\Traits;

trait VerifiesRuleViolations
{
    // TODO: Factor in adjustments to runtime
    public function violatesRule($rule)
    {
        // Prevent rules from firing if no pings are present. Special case for newly created monitors
        if (!$this->pings->count()) {
            return;
        }

        // TODO: Remember how to check for an instance of a class. Derp derp.
//        if (! $rule instanceof Rule::class) {
//            $var = $this->rules()->whereName($rule)->first();
//            return $this->violatesRule($var);
//        }

        $functionName = 'verify' . studly_case($rule->name) . 'Violation';

        return $this->{$functionName}();
    }

    public function verifyCronDidNotRunViolation()
    {
        if (!$this->lastPing('run')) return;
        // Check for the last run endpoint
        if ($this->lastPingDate('run')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }

    public function verifyCronDidNotCompleteViolation()
    {
        if (!$this->lastPing('complete')) return;
        // Check for the last complete endpoint
        if ($this->lastPingDate('complete')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }

    public function verifyIncomingHeartbeatDidNotCompleteViolation()
    {
        if (!$this->lastPing('heartbeat', 'incoming')) return;
        // Check for the last heartbeat endpoint
        if ($this->lastPingDate('heartbeat', 'incoming')->format('Y-m-d H:i') < $this->lastExpectedRunDate()->format('Y-m-d H:i')) {
            return true;
        }

        return false;
    }
}