<x-mail::message>
# {{ $title }}

{{ $messageBody }}

@if ($link)
<x-mail::button :url="$link">
{{ __('Voir les détails') }}
</x-mail::button>
@endif

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
