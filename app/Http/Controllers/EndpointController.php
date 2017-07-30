<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;

class EndpointController extends Controller
{
    public function runEndpoint(Monitor $monitor)
    {
        if ($monitor->type !== 'cron') {
            abort(404); // Prevent cross-types
        }

        $ping = $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'run',
            'ip' => request()->ip(),
        ]);

        return response()->json([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
            'ping' => $ping,
        ], 200);
    }

    public function completeEndpoint(Monitor $monitor)
    {
        if ($monitor->type !== 'cron') {
            abort(404); // Prevent cross-types.
        }

        $lastRunPing = $monitor->lastPing('run');

        $ping = $monitor->pings()->create([
            'pair_id' => $lastRunPing ? $lastRunPing->id : null,
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'complete',
            'ip' => request()->ip(),
        ]);

        // If monitor has a run ping, associate the complete ping with it.
        if ($lastRunPing) {
            $lastRunPing->pair()->associate($ping)->save();
        }

        // Reset notifications
        $monitor->delay_until = null;
        $monitor->save();

        return response()->json([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
            'ping' => $ping->load('pair'),
        ], 200);
    }

    public function heartbeatEndpoint(Monitor $monitor)
    {
        // TODO: Change name to incoming-heartbeat?
        if ($monitor->type !== 'heartbeat') {
            abort(404); // Prevent cross-types
        }

        $ping = $monitor->pings()->create([
            'type' => 'incoming',
            'status' => 'success',
            'endpoint' => 'heartbeat',
            'ip' => request()->ip(),
        ]);

        // Reset notifications
        $monitor->delay_until = null;
        $monitor->save();

        return response()->json([
            'status' => 'success', // TODO: Maybe change this to 'ok'?
            'ping' => $ping,
        ], 200);
    }
}
