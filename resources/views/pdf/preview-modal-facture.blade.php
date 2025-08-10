<div class="w-full">
    <!-- Barre d'actions uniquement -->
    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
        <div class="flex items-center justify-center">
            <p class="text-sm text-gray-600 font-medium">
                🧾 Aperçu PDF généré avec react-pdf/renderer
            </p>
        </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mb-4 flex gap-3">
        <button 
            onclick="openFullPdf()"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2M17 6l5 5m-5-5v5m0-5h5"></path>
            </svg>
            Ouvrir en plein écran
        </button>
        
        <button 
            onclick="printPdf()"
            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimer
        </button>
        
        <button 
            onclick="downloadPdf()"
            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Télécharger
        </button>
    </div>

    <!-- Aperçu PDF dans iframe -->
    <div class="border rounded-lg overflow-hidden bg-white" style="height: 700px;">
        <iframe 
            id="pdfFrame"
            src="{{ $pdfUrl }}"
            class="w-full h-full border-0"
            loading="lazy"
            title="Aperçu PDF de la facture {{ $facture->numero_facture }}"
        >
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Votre navigateur ne supporte pas l'affichage des PDFs.</p>
                    <a href="{{ $pdfUrl }}" target="_blank" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                        Ouvrir le PDF dans un nouvel onglet
                    </a>
                </div>
            </div>
        </iframe>
    </div>

    <!-- Actions rapides en bas -->
    <div class="mt-4 flex justify-center">
        <p class="text-xs text-gray-500">
            💡 Utilisez les boutons ci-dessus pour imprimer, télécharger ou ouvrir en plein écran
        </p>
    </div>
</div>

<script>
// Fonctions JavaScript pour les boutons d'action
function openFullPdf() {
    window.open('{{ $pdfUrl }}', '_blank');
}

function printPdf() {
    const iframe = document.getElementById('pdfFrame');
    if (iframe.contentWindow) {
        iframe.contentWindow.print();
    } else {
        // Fallback - ouvrir et imprimer
        window.open('{{ $pdfUrl }}', '_blank');
    }
}

function downloadPdf() {
    // Créer un lien de téléchargement
    const link = document.createElement('a');
    link.href = '{{ $pdfUrl }}';
    link.download = 'facture_{{ $facture->numero_facture }}.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Gestion du chargement de l'iframe
document.getElementById('pdfFrame').addEventListener('load', function() {
    console.log('PDF facture chargé avec succès dans le modal');
});

document.getElementById('pdfFrame').addEventListener('error', function() {
    console.error('Erreur lors du chargement du PDF facture');
    this.innerHTML = `
        <div class="flex items-center justify-center h-full">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2 text-sm text-red-600">Erreur lors du chargement du PDF</p>
                <a href="{{ $pdfUrl }}" target="_blank" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                    Ouvrir dans un nouvel onglet
                </a>
            </div>
        </div>
    `;
});
</script>
