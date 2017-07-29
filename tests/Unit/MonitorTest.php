<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Ping;
use App\Models\Monitor;
use Cron\CronExpression;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MonitorTest extends TestCase
{
    use DatabaseMigrations;

    protected $monitor;

    public function setUp()
    {
        parent::setUp();
        $this->monitor = create(Monitor::class);
    }

    /** @test */
    public function it_has_an_owner()
    {
        $this->assertInstanceOf(User::class, $this->monitor->owner);
    }
    
    /** @test */
    public function it_has_pings()
    {
        $ping = create(Ping::class, [
            'monitor_id' => $this->monitor->id
        ]);

        $this->assertInstanceOf(Collection::class, $this->monitor->pings);
    }

    /** @test */
    public function it_has_notifications()
    {
        $this->assertInstanceOf(Collection::class, $this->monitor->notifications);
    }
    
    /** @test */
    public function it_has_notification_channels()
    {
        $this->assertInstanceOf(Collection::class, $this->monitor->notificationChannels);
    }

    /** @test */
    public function an_expression_must_convert_to_an_object()
    {
        $this->assertInstanceOf(CronExpression::class, $this->monitor->expressionInstance());
    }

    /** @test */
    public function its_last_run_date_must_be_an_instance_of_carbon()
    {
        $ping = create(Ping::class, [
            'monitor_id' => $this->monitor->id,
            'endpoint' => 'run'
        ]);

        $this->assertInstanceOf(Carbon::class, $this->monitor->lastPingDate('run'));
    }

    /** @test */
    public function its_last_complete_date_must_be_an_instance_of_carbon()
    {
        $ping = create(Ping::class, [
            'monitor_id' => $this->monitor->id,
            'endpoint' => 'complete'
        ]);

        $this->assertInstanceOf(Carbon::class, $this->monitor->lastPingDate('complete'));
    }

    /** @test */
    public function its_last_expected_run_date_must_be_an_instance_of_a_timestamp_object()
    {
        $this->assertInstanceOf(Carbon::class, $this->monitor->lastExpectedRunDate());
    }

    /** @test */
    public function its_next_expected_run_date_must_be_an_instance_of_a_timestamp_object()
    {
        $this->assertInstanceOf(Carbon::class, $this->monitor->nextExpectedRunDate());
    }

}
