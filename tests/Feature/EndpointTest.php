<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EndpointTest extends TestCase
{
    use DatabaseMigrations;

    protected $monitor;

    public function setUp()
    {
        parent::setUp();
        // This should return a 'cron' type monitor
        $this->monitor = create(Monitor::class);
    }

    /** @test */
    public function the_run_endpoint_will_create_a_ping()
    {
        // TODO: Figure out how to make wildcards in assertJson...
        $response = $this->json('GET', '/m/' . $this->monitor->shortcode . '/run');
        $response->assertJson([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
//            'ping' => [
//                'type' => '*',
//                'status' => '*',
//                'endpoint' => '*',
//                'ip' => '*',
//                'monitor_id' => '*',
//                'updated_at' => '*',
//                'created_at' => '*',
//                'id' => '*',
//            ]
        ]);
        $ping = (json_decode($response->getContent()))->ping;
        $this->assertDatabaseHas('pings', [
            'id' => $ping->id
        ]);
    }

    /** @test */
    public function the_complete_endpoint_will_create_a_ping()
    {
        // TODO: Figure out how to make wildcards in assertJson...
        $response = $this->json('GET', '/m/' . $this->monitor->shortcode . '/complete');
        $response->assertJson([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
//            'ping' => [
//                'type' => '*',
//                'status' => '*',
//                'endpoint' => '*',
//                'ip' => '*',
//                'monitor_id' => '*',
//                'updated_at' => '*',
//                'created_at' => '*',
//                'id' => '*',
//            ]
        ]);
        $ping = (json_decode($response->getContent()))->ping;
        $this->assertDatabaseHas('pings', [
            'id' => $ping->id
        ]);
    }

    /** @test */
    public function the_heartbeat_endpoint_will_create_a_ping()
    {
        $heartbeatMonitor = create(Monitor::class, [
            'type' => 'heartbeat'
        ]);

        // TODO: Figure out how to make wildcards in assertJson...
        $response = $this->json('GET', '/m/' . $heartbeatMonitor->shortcode . '/heartbeat');
        $response->assertJson([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
//            'ping' => [
//                'type' => '*',
//                'status' => '*',
//                'endpoint' => '*',
//                'ip' => '*',
//                'monitor_id' => '*',
//                'updated_at' => '*',
//                'created_at' => '*',
//                'id' => '*',
//            ]
        ]);
        $ping = (json_decode($response->getContent()))->ping;
        $this->assertDatabaseHas('pings', [
            'id' => $ping->id
        ]);
    }

    /** @test */
    public function notification_delays_are_reset_when_a_cron_completes()
    {
        $this->monitor->delay_until = Carbon::now()->addHours(6);
        $this->monitor->save();

        $this->json('GET', '/m/' . $this->monitor->shortcode . '/complete');

        $this->assertNull($this->monitor->fresh()->delay_until);
    }

    /** @test */
    public function notification_delays_are_reset_when_a_heartbeat_completes()
    {
        $heartbeatMonitor = create(Monitor::class, [
            'type' => 'heartbeat',
            'delay_until' => Carbon::now()->addHours(6)
        ]);

        $this->json('GET', '/m/' . $heartbeatMonitor->shortcode . '/heartbeat');

        $this->assertNull($heartbeatMonitor->fresh()->delay_until);
    }
}
