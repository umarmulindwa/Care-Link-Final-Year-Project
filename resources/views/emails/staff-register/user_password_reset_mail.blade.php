@component('mail::message')
Hello {{ $user_name }},

Your password has been reset on the UNICEF {{ env('VITE_COUNTRY_OFFICE') }} Digital Operations
Platform, you can now login
with.<br>

<b>Email:</b> {{ $email_address }} <br>
<b>Password:</b> {{ $new_password }}

<br>
@component('mail::button', ['url' => env('APP_URL')])
Login
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
