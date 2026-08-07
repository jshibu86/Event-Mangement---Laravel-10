@component('mail::message')
# A message from {{ $brandName }}

@component('mail::panel')
{!! $bodyHtml !!}
@endcomponent

If you have any questions, please reply to this email and our event team will be glad to help.

Thanks,<br>
**The {{ $brandName }} Team**
@endcomponent
