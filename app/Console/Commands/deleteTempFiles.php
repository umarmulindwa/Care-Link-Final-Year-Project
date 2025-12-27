<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class deleteTempFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tempfiles:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command remove all temporary files from the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Storage::disk('public')->deleteDirectory('invoice_temp_files');
    }
}
