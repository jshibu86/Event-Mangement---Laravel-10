@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (!empty($mailLogoPath))
<img src="{{ $message->embed($mailLogoPath) }}" class="logo" alt="{{ $brandName ?? 'Festiva' }} logo">
@else
<span class="brand-name">{{ $brandName ?? $slot }}</span>
@endif
</a>
</td>
</tr>
