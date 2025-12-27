<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DollarRate;
use Carbon\Carbon;
use Goutte\Client;
use Illuminate\Support\Facades\Log;


class ForexRateUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forex-rate:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Every After 15 minutes, connect to UN site and import data on the rate, then use this as the basis to calculate the forex rates.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            // fetch operational rates
            $websiteData = collect($this->getWebsiteData());

            $this->line(json_encode($websiteData->all()));
            $usd = collect($websiteData)->where("Currency_short", "MWK")->first()['Rate'];
            foreach ($websiteData as $datum) {
                $rate = (float)$datum['Rate'];
                // $rate_to_1 = 1/$rate;
                // $rate_to_mwk = $rate_to_1*$usd;
                $stored_record = DollarRate::where("currency", $datum['Currency_short'])->first();

                if ($stored_record) {
                    $stored_record->update([
                        "currency" => $datum['Currency_short'],
                        "rate" => $rate,
                         "updated_at" => Carbon::now()
                    ]);
                } else {
                    DollarRate::updateOrCreate(
                        ["currency" => $datum['Currency_short']],
                        [
                            "currency" => $datum['Currency_short'],
                            "rate" => $rate
                        ]
                    );
                }
            }

            return "Updated forex rates successfully";
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            Log::error("Email logs update failed " . $ex->getMessage());
        }
    }

    public function getWebsiteData()
    {
        // $optons = [ "INdtmFROM"=>"","INstrRATECODE"=>"MWK"];
        $client = new Client();
        $url = 'https://treasury.un.org/operationalrates/xsqlExRates.php';
        $page = $client->request('POST', $url);
        $page->filter('table tr ')
        ->each(function ($node, $key) {
            $this->results[$node->filter('td')->eq(1)->text()] = [
                "Country" => $node->filter('td')->eq(0)->text(),
                "Currency_short" => $node->filter('td')->eq(1)->text(),
                "Currency_long" => $node->filter('td')->eq(2)->text(),
                "Rate" => $node->filter('td')->eq(3)->text(),
                "Date" => $node->filter('td')->eq(4)->text()
            ];
        });
        return $this->results;
    }
}
