<x-mail::message>
# {{ $subject }}

{{ $body }}

<x-mail::button :url="url('/login')">
Buka KerjaanKu
</x-mail::button>

Terima kasih,<br>
{{ $senderName }}
</x-mail::message>
