<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Facture;
use App\Models\Madinia;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PdfController extends Controller
{
    /**
     * Générer un PDF pour un devis
     */
    public function generateDevisPdf(Devis $devis)
    {
        // Vérifier que l'utilisateur est connecté
        if (! Auth::check()) {
            abort(403, 'Accès non autorisé');
        }

        $madinia = Madinia::query()->first();

        return Inertia::render('PdfGenerator', [
            'type' => 'devis',
            'document' => $devis->load([
                'lignes.service',
                'client.entreprise',
            ]),
            'madinia' => $madinia,
            'user' => Auth::user(),
            'title' => "Devis {$devis->numero_devis}",
        ]);
    }

    /**
     * Générer un PDF pour une facture
     */
    public function generateFacturePdf(Facture $facture)
    {
        // Vérifier que l'utilisateur est connecté
        if (! Auth::check()) {
            abort(403, 'Accès non autorisé');
        }

        $madinia = Madinia::query()->first();

        return Inertia::render('PdfGenerator', [
            'type' => 'facture',
            'document' => $facture->load([
                'lignes.service',
                'client.entreprise',
                'devis',
            ]),
            'madinia' => $madinia,
            'user' => Auth::user(),
            'title' => "Facture {$facture->numero_facture}",
        ]);
    }
}
