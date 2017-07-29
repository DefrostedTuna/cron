@component('mail::message')
# {{ $monitor->name }}

This monitor is **overdue** and has not checked in.

**Expected:**

Monitor did not run.


{{--This monitor is currently {something-minutes} overdue.--}}
{{--If you would like to pause these notifications, please let us know.--}}
{{--{{ button here to allow pausing  }}--}}

@component('mail::button', ['url' => $url])
View this monitor
@endcomponent

Thanks,<br>
Tuna
@endcomponent