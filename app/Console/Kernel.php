<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
// use Predis\Configuration\Option\Commands;

class Kernel extends ConsoleKernel
{
    protected $commands =[
        Commands\ForexRateUpdate::class,
        Commands\deleteTempFiles::class,
        Commands\SendTestMail::class,
        Commands\StoreCacheCommand::class,
        Commands\ClearSessionsCommand::class,
    ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('tempfiles:delete')->cron('0 3 * * *');
        $schedule->command('forex-rate:fetch')->everyFifteenMinutes();

        $schedule->command('send-test:mail "' . "TESTMAIL SSD-PROD-" . date("YMdHi").'"')->cron('30 7 * * 1,3');
        $schedule->command("mailer:dispatch-pending-mails")->cron("*/3 * * * *");
        $schedule->command("app:clear-sessions")->cron("0,30 * * * *");
        // $schedule->command("queue:restart")->cron("*/8 * * * *");
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
