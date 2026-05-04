<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove notifications older than 1 month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = DB::table('notifications')
            ->where('created_at', '<', now()->subMonth())
            ->delete();

        $this->info("Successfully deleted {$count} old notifications.");
    }
}
