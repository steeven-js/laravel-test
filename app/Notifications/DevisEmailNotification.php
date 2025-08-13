<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DevisEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Devis $devis,
        public bool $success,
        public ?string $errorMessage = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        // Persistée en base; adaptable selon env si besoin
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'devis_email',
            'status' => $this->success ? 'success' : 'error',
            'message' => $this->success ? 'Email devis envoyé avec succès' : ("Echec envoi email devis: " . ($this->errorMessage ?? '')),
            'devis_id' => $this->devis->id,
            'numero_devis' => $this->devis->numero_devis,
            'pdf_url' => $this->devis->pdf_url,
        ];
    }
}


