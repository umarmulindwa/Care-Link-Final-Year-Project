<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;

class SendTestMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-test:mail {title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command send test mail from the server.';

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
        try {
            // lmwaisunga@unicef.org
            // $to = "lmwaisunga@unicef.org";
            $to = "juliusjuniorkazibwe@gmail.com,otimsamuel@gmail.com";
            $subject = "Testing " . $this->argument('title');
            $message = "This is a test schedule mail!";


            $mail = new TestMail($subject, $message);
            Log::info(exec("whoami") . "\n");
            $staff = new stdClass();
            $staff->name = "Otim Samuel";
            $staff->email = "otimsamuel@gmail.com";

            $staffCC = new stdClass();
            $staffCC->name = "Technical Team";
            $staffCC->email = "tech@trailanalytics.com";

            // Mail::to($staff)->cc($staffCC)->send($mail);
            globalSendEmail($staff, $subject, $mail, "juliusjuniorkazibwe@gmail.com", true, 'BG-TRIGGED', NULL);
            globalSendEmail($staffCC, $subject, $mail, $staff->email, true, 'BG-TRIGGED', NULL);




            Log::info($subject . " has been sent\n");
        } catch (\Throwable $th) {
            echo $th->getMessage() . "\n";
            Log::error($th->getMessage() . "\n");
        }
    }
}
