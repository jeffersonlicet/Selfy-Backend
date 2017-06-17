@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@endif

{{-- Body --}}
{{ $body }}

<!-- Salutation -->
Regards,<br>The Selfy team.
@endcomponent
