<?php

namespace App\Queries;

use Carbon\Carbon;

trait CommonMonitorQueries
{
    public function getActiveEntries()
    {
        return $this->with('pings')->with('rules')->where('paused', false)
            ->where(function ($query) {
                $query->where('delay_until', null)
                    ->orWhere('delay_until', '<=', Carbon::now()->addSeconds(30));
            })
            ->get();
    }
}
