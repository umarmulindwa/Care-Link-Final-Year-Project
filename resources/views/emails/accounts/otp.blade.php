@component('mail::message')
Hello

Your one-time password (OTP) for authentication is: <b> {{ $token }}</b>.  Please use this OTP for the requested action. It will expire at {{ $expires_at->format('h:i A') }}

Regards,<br>
{{ config('app.name') }}
@endcomponent
