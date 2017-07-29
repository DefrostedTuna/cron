<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Ping;
use App\Models\Monitor;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PingTest extends TestCase
{
    use DatabaseMigrations;

    protected $ping;

    public function setUp()
    {
        parent::setUp();
        $this->ping = create(Ping::class);
    }

    /** @test */
    public function a_ping_belongs_to_a_monitor()
    {
        $this->assertInstanceOf(Monitor::class, $this->ping->monitor);
    }

    /** @test */
    public function a_ping_can_have_a_pair()
    {
        $runPing = create(Ping::class, [
            'endpoint' => 'run',
        ]);

        $completePing = create(Ping::class, [
            'monitor_id' => $runPing->monitor_id,
            'pair_id' => $runPing->id,
            'endpoint' => 'complete'
        ]);

        $runPing->pair()->associate($completePing)->save();

        $this->assertInstanceOf(Ping::class, $runPing->pair);
        $this->assertInstanceOf(Ping::class, $completePing->pair);
    }
}
