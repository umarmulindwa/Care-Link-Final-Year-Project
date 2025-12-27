@component('mail::message')
<p style="color: #6b7280">Hello {{ $adminName }},</p>

<p style="color: #6b7280">The following support request has been received:</p>

<p><strong>From: </strong> <span style="color: #6b7280">{{ $issueDetails['name'] }} ({{ $issueDetails['email'] }})</span></p>

<p><strong>Type of Request: </strong> <span style="color: #6b7280">{{ $issueDetails['issue_category'] }}</span></p>

<p><strong>Issue: </strong> <span style="color: #6b7280">{{ $issueDetails['issue'] }}</span></p>

<p><strong>Detailed Description: </strong> <span style="color: #6b7280">{!! $issueDetails['description'] !!}</span></p>

<p style="color: #6b7280">Kindly therefore logon to the platform, and endeavor to resolve the issue at the soonest.</p>

@component('mail::button', ['url' => env('PLATFORM_URL')])
    LOGIN
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
