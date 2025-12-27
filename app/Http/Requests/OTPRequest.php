<?php

namespace App\Http\Requests;

use App\Mail\sendOtpMail;
use App\Models\OTP;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OTPRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required',
        ];
    }

    /**
     * @throws ValidationException
     * @throws HttpResponseException
     */
    public function authenticate(){
        $this->ensureIsNotRateLimited();

        if (!$this->validate() || !Auth::loginUsingId($this->find_user()?->id)) {
            RateLimiter::hit($this->throttleKey());

            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        'email' => __('auth.otp'),
                    ],
                ], 422)
            );
        }

        OTP::query()->where('user_id',$this->find_user()->id)->delete();
        RateLimiter::clear($this->throttleKey());
    }

    public function find_user(): object|null
    {
        return User::query()->where('email',$this->email)->first();
    }

    /**
     * @throws Exception
     */
    public function sendOTP(){
        $user = $this->find_user();
        $token = rand(1000, 9999);
        $expires_at = Carbon::now()->addMinutes(10);

        $otp = new OTP();
        $otp->user_id = $user->id;
        $otp->token = Hash::make($token);
        $otp->expires_at = $expires_at;
        $otp->save();

        $mailable = new sendOtpMail($token,$expires_at);
        globalSendEmail($user,"OTP",$mailable);
    }

    public function validate(): bool
    {
        $user = $this->find_user();

        if(!$user) return false;

        $latestOTP = OTP::query()
            ->where('user_id',$user->id)
            ->where('expires_at','>',Carbon::now())
            ->latest()
            ->first();

        if(!$latestOTP) return false;

        return Hash::check($this->token, $latestOTP->token);

    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws ValidationException
     * @throws HttpResponseException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new HttpResponseException(
            response()->json([
                'errors' => [
                    'email' => trans('auth.otp_throttle', [
                        'seconds' => $seconds,
                        'minutes' => ceil($seconds / 60),
                    ]),
                ],
            ], 422)
        );
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }



}
