@component('mail::message')
<p style="color: #6b7280">Hello {{ $supportDetails['origin_name'] }},</p>

<br>
<p style="color: #6b7280">The following support response has been provided by {{ $supportDetails['admin_name'] }} ({{ $supportDetails['admin_email'] }}):</p>
<br>


<p><strong>Issue: </strong> <span style="color: #6b7280">{{ $supportDetails['issue'] }}</span></p>

<p><strong>Response: </strong> <span style="color: #6b7280">{!! $supportDetails['response'] !!}</span></p>

<br>
<p style="color: #6b7280">Click the button to respond to this request</p>

@component('mail::button', ['url' => env('APP_URL')."/support"])
    RESPOND TO REQUEST
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
