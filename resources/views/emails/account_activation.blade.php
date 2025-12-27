@component('mail::message')
Hello {{ $name }},

A new Service Provider has registered an account on the UNICEF {{ env('VITE_COUNTRY_OFFICE') }} Digital Operations
Platform .<br>

Please Login and activate their account. <br>

@component('mail::button', ['url' => env('APP_URL')])
Login
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
