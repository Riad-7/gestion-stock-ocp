@props(['url'])
@php
    $logoPath = public_path('logoocp-removebg-preview.png');
    $logoSrc = null;

    if (is_file($logoPath)) {
        $mimeType = mime_content_type($logoPath) ?: 'image/png';
        $logoSrc = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logoSrc)
<img src="{{ $logoSrc }}" class="logo" alt="{{ config('app.name', 'GS OCP') }} Logo">
@else
{{ config('app.name', 'GS OCP') }}
@endif
</a>
</td>
</tr>
