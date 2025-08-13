<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <div class="text-sm text-slate-500">From</div>
            <div class="font-medium">{{ $from ?: '—' }}</div>
        </div>
        <div>
            <div class="text-sm text-slate-500">To</div>
            <div class="font-medium">{{ $to ?: '—' }}</div>
        </div>
        <div>
            <div class="text-sm text-slate-500">Cc</div>
            <div class="font-medium">{{ $cc ?: '—' }}</div>
        </div>
        <div>
            <div class="text-sm text-slate-500">Pièce jointe PDF</div>
            <div class="font-medium">
                @if($includePdf)
                    Oui @if($pdfUrl) (<a href="{{ $pdfUrl }}" target="_blank" class="text-primary-600 underline">voir</a>) @endif
                @else
                    Non
                @endif
            </div>
        </div>
        <div>
            <div class="text-sm text-slate-500">Inclure URL dans le message</div>
            <div class="font-medium">{{ $includeUrl ? 'Oui' : 'Non' }}</div>
        </div>
    </div>

    <div class="space-y-2">
        <div class="text-sm text-slate-500">Objet</div>
        <div class="font-semibold">{{ $subject }}</div>
    </div>

    <div class="space-y-2">
        <div class="text-sm text-slate-500">Corps</div>
        <div class="prose max-w-none">{!! $body !!}</div>
    </div>

    <div class="rounded-md bg-slate-50 p-3 text-xs text-slate-600">
        <div><span class="font-semibold">Devis:</span> {{ $devis->numero_devis }} — {{ number_format((float) $devis->montant_ttc, 2, ',', ' ') }} € TTC</div>
        @if($pdfUrl)
            <div><span class="font-semibold">URL PDF:</span> <a href="{{ $pdfUrl }}" class="underline" target="_blank">{{ $pdfUrl }}</a></div>
        @endif
    </div>
</div>


