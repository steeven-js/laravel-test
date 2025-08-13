<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Historique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait HasHistorique
{
    /**
     * Relation morphMany vers l'historique
     */
    public function historiques(): MorphMany
    {
        return $this->morphMany(Historique::class, 'entite', 'entite_type', 'entite_id');
    }

    /**
     * Enregistrer une action dans l'historique
     */
    public function enregistrerAction(
        string $action,
        string $titre,
        ?string $description = null,
        ?array $donneesAvant = null,
        ?array $donneesApres = null,
        ?array $donneesSupplementaires = null
    ): void {
        if (! Auth::check()) {
            return;
        }

        $user = Auth::user();

        Historique::create([
            'entite_type' => static::class,
            'entite_id' => $this->id,
            'action' => $action,
            'titre' => $titre,
            'description' => $description,
            'donnees_avant' => $donneesAvant,
            'donnees_apres' => $donneesApres,
            'donnees_supplementaires' => $donneesSupplementaires,
            'user_id' => $user->id,
            'user_nom' => $user->name,
            'user_email' => $user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Enregistrer la création
     */
    public function enregistrerCreation(): void
    {
        $this->enregistrerAction(
            'creation',
            'Création de ' . class_basename($this) . " #{$this->id}",
            'Nouvel enregistrement créé',
            null,
            $this->getAttributes(),
            ['id' => $this->id]
        );
    }

    /**
     * Enregistrer la modification
     */
    public function enregistrerModification(array $changes, array $original): void
    {
        if (empty($changes)) {
            return;
        }

        $donneesAvant = [];
        $donneesApres = [];

        foreach ($changes as $field => $newValue) {
            $donneesAvant[$field] = $original[$field] ?? null;
            $donneesApres[$field] = $newValue;
        }

        $this->enregistrerAction(
            'modification',
            'Modification de ' . class_basename($this) . " #{$this->id}",
            'Données mises à jour',
            $donneesAvant,
            $donneesApres
        );
    }

    /**
     * Enregistrer la suppression
     */
    public function enregistrerSuppression(): void
    {
        $this->enregistrerAction(
            'suppression',
            'Suppression de ' . class_basename($this) . " #{$this->id}",
            'Enregistrement supprimé',
            $this->getAttributes(),
            null
        );
    }

    /**
     * Enregistrer un changement de statut
     */
    public function enregistrerChangementStatut(string $ancienStatut, string $nouveauStatut, ?string $raison = null): void
    {
        $this->enregistrerAction(
            'changement_statut',
            'Changement de statut de ' . class_basename($this) . " #{$this->id}",
            $raison ?? "Statut changé de {$ancienStatut} vers {$nouveauStatut}",
            ['statut' => $ancienStatut],
            ['statut' => $nouveauStatut],
            ['raison' => $raison]
        );
    }

    /**
     * Enregistrer une action personnalisée
     */
    public function enregistrerActionPersonnalisee(
        string $action,
        string $titre,
        ?string $description = null,
        ?array $donnees = null
    ): void {
        $this->enregistrerAction(
            $action,
            $titre,
            $description,
            null,
            $donnees
        );
    }

    /**
     * Boot du trait pour enregistrer automatiquement les actions
     */
    protected static function bootHasHistorique(): void
    {
        static::created(function (Model $model) {
            if (method_exists($model, 'enregistrerCreation')) {
                $model->enregistrerCreation();
            }
        });

        static::updated(function (Model $model) {
            if (method_exists($model, 'enregistrerModification')) {
                $model->enregistrerModification(
                    $model->getChanges(),
                    $model->getOriginal()
                );
            }
        });

        static::deleted(function (Model $model) {
            if (method_exists($model, 'enregistrerSuppression')) {
                $model->enregistrerSuppression();
            }
        });
    }
}
