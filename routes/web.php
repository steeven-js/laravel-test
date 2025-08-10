<?php

declare(strict_types=1);

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Routes PDF avec Inertia + React
Route::middleware(['auth'])->group(function () {
    Route::get('/devis/{devis}/pdf', [PdfController::class, 'generateDevisPdf'])
        ->name('devis.pdf');

    Route::get('/factures/{facture}/pdf', [PdfController::class, 'generateFacturePdf'])
        ->name('factures.pdf');
});
