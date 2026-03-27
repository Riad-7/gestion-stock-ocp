{{ $subject }}

Bonjour Admin,

{{ $intro }}

@foreach ($details as $label => $value)
{{ $label }}: {{ $value }}
@endforeach

Cet email a ete envoye automatiquement par le systeme de gestion de stock.
