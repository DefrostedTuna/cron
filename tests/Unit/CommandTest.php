<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Monitor;
use App\Jobs\EvaluateRuleViolations;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CommandTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function monitor_check_pushes_a_rule_check_job_to_the_queue()
    {
        $this->expectsJobs(EvaluateRuleViolations::class);

        // Create, not make. Derp.
        create(Monitor::class);

        \Artisan::call('monitor:check');
    }
}
