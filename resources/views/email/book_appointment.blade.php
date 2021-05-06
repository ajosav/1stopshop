@component('mail::message')
Hi {{$receiver->first_name}}

Good day, I am {{$sender->first_name}} {{$sender->last_name}}. I reviewed your services on 1Automech, which caught my interest.

<br>

I would love to make use of your service; I will be coming to your office on {{$appointment['date']}} {{$appointment['time']}} {{$appointment['meridian']}}

@if(array_key_exists('description', $appointment))
<br>
More Details: {{$appointment['description']}}

<br>
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
