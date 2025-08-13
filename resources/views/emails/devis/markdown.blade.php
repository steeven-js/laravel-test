@component('mail::message')

{!! nl2br(e($markdownBody)) !!}

@if($includeUrl && filled($devis->pdf_url))
@component('mail::button', ['url' => $devis->pdf_url])
Télécharger le devis (PDF)
@endcomponent
@endif

Merci,
{{ config('app.name') }}

@endcomponent


