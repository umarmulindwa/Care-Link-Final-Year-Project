<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\TemporaryPasswordNewAccountMail;
use App\Models\StaffProfile;
use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Str;




class SeedUnicefStaffJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $newUser;

    public function __construct(StaffProfile $newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //

        //send email to account created with temporary password
        try {
            //adding staff to the users' table and generating a random password

            // $randomPassword = "unicef#" . mt_rand(1000, 9999);
            $randomPassword = "unicef#93204";

            $createUserObj = new CreateNewUser;

            $createUserObj->create([
                'name' => $this->newUser->name,
                'email' => $this->newUser->email,
                'password' => $randomPassword,
                'password_confirmation' => $randomPassword,
                'terms' => true,
                'newStaff' => true,
            ]);
            $userAccount = User::where('email', $this->newUser->email)->first();
            $userAccount->email_verified_at = date("Y-m-d H:i:s");
            $userAccount->user_type = 'unicef';
            $userAccount->save();
        } catch (\Exception $ex) {
            print_r($ex);
        }
    }
}
