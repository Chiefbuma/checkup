<?php

use App\Livewire\ViewWellnessReport;
use App\Models\Registration;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/home'); // Redirect to Filament
});

Route::get('/wellness-report/{registration}/download', function (Registration $registration) {
    $component = app(ViewWellnessReport::class);
    $component->mount($registration);
    return $component->downloadPdf();
})->name('livewire.download-wellness-report');

Route::get('/storage/pdfs/{filename}', function ($filename) {
    $path = storage_path('app/public/pdfs/' . $filename);
    if (!file_exists($path)) {
        \Log::error('PDF not found for serving: ' . $path);
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"',
    ]);
})->where('filename', '.*')->name('serve.pdf');