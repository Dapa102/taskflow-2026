<x-mail::message>
# {{ $subject }}

{{ $body }}

<x-mail::button :url="url('/login')">
Buka TaskFlow
</x-mail::button>

Terima kasih,<br>
{{ $senderName }}
</x-mail::message>
