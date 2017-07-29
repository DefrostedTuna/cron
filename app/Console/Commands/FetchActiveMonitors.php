<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Monitor;
use Illuminate\Console\Command;
use App\Jobs\EvaluateRuleViolations;

class FetchActiveMonitors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches active monitors from the database, then places them in a queue for processing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Checking database for overdue crons, please wait...');

        foreach ($monitors = (new Monitor)->getActiveEntries() as $monitor) {
            dispatch((new EvaluateRuleViolations($monitor))->onQueue('rules'));
        }

        $this->info('Finished. All crons have been added to a queue.');
    }
}
