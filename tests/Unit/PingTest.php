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
}
