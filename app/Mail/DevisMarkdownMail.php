<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DevisMarkdownMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $markdownBody,
        public Devis $devis,
        public bool $includeUrl = true,
    ) {}

    public function build(): self
    {
        return $this
            ->subject($this->subjectLine)
            ->markdown('emails.devis.markdown', [
                'markdownBody' => $this->markdownBody,
                'devis' => $this->devis,
                'includeUrl' => $this->includeUrl,
            ]);
    }
}
