@component('mail::message')
# Introduction
Hello <strong>{{ $event->user->name }}</strong>

Please click the below link to confirm your email

@component('mail::button', ['url' => route('confirm.registered.email',['confirmation_token'=>$event->user->confirmation_token]) ])

Confirm Email

@endcomponent

Thanks,<br>
<strong>{{ config('app.name') }}</strong> team
@endcomponent
