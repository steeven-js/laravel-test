<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class EmailTemplateRenderer
{
    /**
     * Rend un texte en remplaçant les variables de type {{ path.to.value }} à partir d'un contexte.
     */
    public function render(string $text, array $context = []): string
    {
        if ($text === '') {
            return $text;
        }

        // Support des filtres sous forme {{ variable|filter:arg }} (ex: {{client.created_at|date:d/m/Y}})
        $pattern = '/\{\{\s*([a-zA-Z0-9_\.]+)\s*(?:\|\s*([a-zA-Z0-9_]+)\s*:?\s*([^}]*)\s*)?\}\}/';

        return (string) preg_replace_callback($pattern, function ($matches) use ($context) {
            $path = trim((string) ($matches[1] ?? ''));
            $filter = isset($matches[2]) ? trim((string) $matches[2]) : null;
            $rawArgs = isset($matches[3]) ? trim((string) $matches[3]) : '';

            // Variable spéciale now
            if ($path === 'now') {
                $value = Carbon::now();
            } else {
                $value = Arr::get($context, $path);
            }

            // Application éventuelle d'un filtre
            if ($filter) {
                $args = $rawArgs !== '' ? array_map('trim', explode(',', $rawArgs)) : [];
                $value = $this->applyFilter($filter, $value, $args);
            }

            if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                return (string) $value;
            }
            if ($value instanceof Carbon) {
                return $value->toDateTimeString();
            }
            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            return '';
        }, $text);
    }

    /**
     * Applique un filtre simple (actuellement: date)
     */
    protected function applyFilter(string $filter, mixed $value, array $args): mixed
    {
        switch ($filter) {
            case 'date':
                $format = $args[0] ?? 'd/m/Y H:i';
                if ($value instanceof Carbon) {
                    return $value->format($format);
                }
                if (is_string($value) && $value !== '') {
                    try {
                        return Carbon::parse($value)->format($format);
                    } catch (\Throwable) {
                        return $value;
                    }
                }

                return $value;
            default:
                return $value;
        }
    }

    /**
     * Rendu Markdown minimal → HTML pour l'aperçu (échappement + sauts de ligne).
     */
    public function renderMarkdown(string $markdown): string
    {
        $escaped = e($markdown);

        return nl2br($escaped);
    }
}
