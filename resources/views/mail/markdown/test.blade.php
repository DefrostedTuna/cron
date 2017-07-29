@component('mail::message')
# {{ $monitor->name }}

This monitor is **overdue** and has not checked in.

**Expected:**
@component('mail::table')
| Endpoint                               | Date                                                   | Time                                                  |
| :------------------------------------- |:------------------------------------------------------ | :---------------------------------------------------- |
| {{ $monitor->lastExpectedEndpoint() }} | {{ $monitor->lastExpectedRunDate()->format('m/d/Y') }} | {{ $monitor->lastExpectedRunDate()->format('g:ia') }} |
@endcomponent

**Last received:**
@component('mail::table')
| Endpoint                             | Date                                           | Time                                          |
| :----------------------------------- |:-----------------------------------------------| :-------------------------------------------- |
| {{ $monitor->lastPing()->endpoint }} | {{ $monitor->lastPing()->created_at->format('m/d/Y') }} | {{ $monitor->lastPing()->created_at->format('g:ia') }} |
@endcomponent

{{--This monitor is currently {something-minutes} overdue.--}}
{{--If you would like to pause these notifications, please let us know.--}}
{{--{{ button here to allow pausing  }}--}}

@component('mail::button', ['url' => $url])
View this monitor
@endcomponent

Thanks,<br>
Tuna
@endcomponent