<?php

namespace App\Console\Commands;

use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;

class DebugListeners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:listeners';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Console command to demonstrate duplicate listeners due to multiple event service providers.';

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
        dd(\Event::getListeners(Registered::class));
    }
}
