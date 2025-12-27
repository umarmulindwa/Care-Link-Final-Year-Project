@component('mail::message')
Hello {{ $new_staff_name }},

Your new account has been created on the UNICEF {{ env('VITE_COUNTRY_OFFICE') }} Digital Operations
Platform, you can now login
with.<br>

<b>Email:</b> {{ $email_address }} <br>
<b>Password:</b> {{ $temporary_password }}

<br>
@component('mail::button', ['url' => env('APP_URL')])
Login
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
