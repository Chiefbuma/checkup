<?php

namespace App\Livewire;

use App\Models\Registration;
use App\Models\Nutrition;
use App\Models\Vital;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ViewWellnessReport extends Component
{
    public Registration $registration;
    public ?Nutrition $nutrition;
    public ?Vital $vital;
    public $pdfUrl;
    public $pdfFilename;

    public function downloadPdf()
    {
        \Log::info('downloadPdf method called for registration: ' . $this->registration->id);

        if (empty($this->pdfFilename)) {
            $this->generatePdf();
        }

        $path = storage_path('app/public/pdfs/' . $this->pdfFilename);

        if (!file_exists($path)) {
            \Log::warning('PDF file not found at: ' . $path . '. Regenerating...');
            $this->generatePdf();
        }

        return response()->download($path, $this->pdfFilename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->pdfFilename . '"',
        ])->deleteFileAfterSend(true);
    }

    protected function generatePdf()
    {
        $this->nutrition = $this->registration->nutritions()->first();
        $this->vital = $this->registration->vitals()->first();

        $pdf = Pdf::loadView('livewire.wellness-report-pdf', [
            'registration' => $this->registration,
            'nutrition' => $this->nutrition,
            'vital' => $this->vital,
            'currentDate' => now()->format('l, dS F Y'), // e.g., Thursday, 25th September 2025
        ])->setPaper('a4', 'portrait');

        $this->pdfFilename = 'wellness-report-' . $this->registration->id . '-' . now()->format('YmdHis') . '.pdf';
        $pdfPath = 'pdfs/' . $this->pdfFilename;
        
        Storage::disk('public')->put($pdfPath, $pdf->output());
        $this->pdfUrl = Storage::disk('public')->url($pdfPath);
    }

    public function mount(Registration $registration): void
    {
        $this->registration = $registration->load(['nutritions', 'vitals']);
        $this->nutrition = $this->registration->nutritions()->first();
        $this->vital = $this->registration->vitals()->first();

        $this->generatePdf();
    }

    public function render()
    {
        return view('livewire.view-wellness-report');
    }
}