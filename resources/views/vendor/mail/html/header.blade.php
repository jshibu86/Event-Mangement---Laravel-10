@props(['url', 'brandName' => null, 'logoData' => null, 'logoName' => null, 'logoMime' => null, 'message' => null])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (!empty($logoData) && !empty($message))
<img src="{{ $message->embedData($logoData, $logoName ?? 'logo.png', $logoMime ?? 'image/png') }}" class="logo" alt="{{ $brandName ?? 'Festiva' }} logo">
@else
<span class="brand-name">{{ $brandName ?? $slot }}</span>
@endif
</a>
</td>
</tr>