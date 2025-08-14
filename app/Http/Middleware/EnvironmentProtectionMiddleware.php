<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Traits\EnvironmentProtection;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnvironmentProtectionMiddleware
{
    use EnvironmentProtection;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si la génération de données est autorisée
        if (!$this->isDataGenerationAllowed()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Génération de données non autorisée',
                    'message' => $this->getEnvironmentErrorMessage(),
                    'environment' => app()->environment(),
                ], 403);
            }

            abort(403, $this->getEnvironmentErrorMessage());
        }

        return $next($request);
    }
}
