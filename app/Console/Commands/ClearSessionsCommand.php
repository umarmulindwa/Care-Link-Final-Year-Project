<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            //code...
            // Clear sessions
            DB::connection("mysql")->statement("DELETE FROM sessions WHERE user_id is null");
        } catch (\Throwable $th) {
            //throw $th;
            $this->error('Failed to clear sessions', ["errors"=>$th->getTraceAsString()]);
        }
    }
}
