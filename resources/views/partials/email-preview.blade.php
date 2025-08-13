@php
    // Vérification de sécurité pour éviter l'erreur "Undefined variable"
    $subject = $subject ?? 'Aucun objet défini';
    $body = $body ?? 'Aucun contenu défini';
    // Si le contenu ne semble pas contenir de HTML, respecter les retours à la ligne
    $looksLikeHtml = is_string($body) && preg_match('/<\w+[^>]*>/', $body);
    $renderedBody = $looksLikeHtml ? $body : nl2br(e($body));
@endphp

<div class="card-flat" style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: .5rem; background: #fff;">
    <div style="display: flex; align-items: center; gap: .5rem; margin-bottom: .5rem;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px; color: #2563eb;"><path d="M4 4h16v16H4z"/><path d="M4 8h16"/></svg>
        <strong>Aperçu rendu</strong>
    </div>
    <div style="font-size: .875rem; color: #6b7280; margin-bottom: .5rem;">Les variables ont été remplacées avec le contexte actuel.</div>
    <div style="border-top: 1px dashed #e5e7eb; padding-top: .75rem;">
        <div style="font-weight: 600; margin-bottom: .5rem;">Objet</div>
        <div>{{ $subject }}</div>
    </div>
    <div style="border-top: 1px dashed #e5e7eb; padding-top: .75rem; margin-top: .75rem;">
        <div style="font-weight: 600; margin-bottom: .5rem;">Contenu</div>
        <div>{!! $renderedBody !!}</div>
    </div>
</div>


