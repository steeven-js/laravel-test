<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Facture;
use App\Models\Madinia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PdfController extends Controller
{
    /**
     * Générer un PDF pour un devis
     */
    public function generateDevisPdf(Devis $devis)
    {
        if (! Auth::check()) {
            abort(403, 'Accès non autorisé');
        }

        // Optimisations pour documents volumineux
        DB::connection()->disableQueryLog();
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $madinia = Madinia::query()->first();

        $devis->load([
            'client:id,nom,adresse,ville,code_postal,entreprise_id',
            'client.entreprise:id,nom',
            'lignes' => fn ($q) => $q->select('id','devis_id','service_id','quantite','unite','prix_unitaire_ht','remise_pourcentage','taux_tva','description_personnalisee','montant_ht','ordre')->orderBy('ordre'),
            'lignes.service:id,nom',
        ]);

        return Inertia::render('PdfGenerator', [
            'type' => 'devis',
            'document' => $devis,
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
        if (! Auth::check()) {
            abort(403, 'Accès non autorisé');
        }

        // Optimisations pour documents volumineux
        DB::connection()->disableQueryLog();
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $madinia = Madinia::query()->first();

        $facture->load([
            'client:id,nom,adresse,ville,code_postal,entreprise_id',
            'client.entreprise:id,nom',
            'devis:id,numero_devis',
            'lignes' => fn ($q) => $q->select('id','facture_id','service_id','quantite','unite','prix_unitaire_ht','remise_pourcentage','taux_tva','description_personnalisee','montant_ht','ordre')->orderBy('ordre'),
            'lignes.service:id,nom',
        ]);

        return Inertia::render('PdfGenerator', [
            'type' => 'facture',
            'document' => $facture,
            'madinia' => $madinia,
            'user' => Auth::user(),
            'title' => "Facture {$facture->numero_facture}",
        ]);
    }
}
