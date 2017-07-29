<?php

namespace Tests\Unit;

use App\Models\Ping;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RuleViolationsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_verifies_that_a_cron_did_not_run()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'cron',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'run',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        $this->assertTrue($monitor->verifyCronDidNotRunViolation());
    }

    /** @test */
    public function it_verifies_that_a_cron_did_not_complete()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'cron',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'complete',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        $this->assertTrue($monitor->verifyCronDidNotCompleteViolation());
    }

    /** @test */
    public function it_verifies_that_a_cron_ran_longer_than_usual()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'cron',
        ]);

        $runPingOne = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'endpoint' => 'run',
            'created_at' => Carbon::now()->subMinutes(1)
        ]);
        $completePingOne = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'pair_id' => $runPingOne->id,
            'endpoint' => 'complete',
            'created_at' => Carbon::now()->subMinutes(1)->addSeconds(10)
        ]);
        $runPingOne->pair()->associate($completePingOne)->save();

        $runPingTwo = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'endpoint' => 'run',
            'created_at' => Carbon::now()
        ]);
        $completePingTwo = create(Ping::class, [
            'monitor_id' => $monitor->id,
            'pair_id' => $runPingTwo->id,
            'endpoint' => 'complete',
            'created_at' => Carbon::now()->addSeconds(30)
        ]);
        $runPingTwo->pair()->associate($completePingTwo)->save();

        $this->assertTrue($monitor->verifyCronRanLongerThanUsualViolation());
    }

    /** @test */
    public function it_verifies_that_an_incoming_heartbeat_did_not_complete()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'heartbeat',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'heartbeat',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(2)
        ]);

        $this->assertTrue($monitor->verifyIncomingHeartbeatDidNotCompleteViolation());
    }

    /** @test */
    public function run_violation_is_not_triggered_when_the_cron_is_on_time()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'cron',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'run',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(1)
        ]);

        $this->assertFalse($monitor->verifyCronDidNotRunViolation());
    }

    /** @test */
    public function complete_violation_is_not_triggered_when_the_cron_is_on_time()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'cron',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'complete',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(1)
        ]);

        $this->assertFalse($monitor->verifyCronDidNotCompleteViolation());
    }

    /** @test */
    public function incoming_heartbeat_violation_is_not_triggered_when_the_heartbeat_is_on_time()
    {
        $monitor = create(Monitor::class, [
            'name' => 'Verify',
            'shortcode' => '123',
            'expression' => '* * * * *',
            'type' => 'heartbeat',
        ]);

        $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'heartbeat',
            'ip' => '127.0.0.1',
            'created_at' => Carbon::now()->subMinutes(1)
        ]);

        $this->assertFalse($monitor->verifyIncomingHeartbeatDidNotCompleteViolation());
    }
}
