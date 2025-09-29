<?php

namespace App\Livewire;

use App\Models\Registration;
use App\Models\Nutrition;
use App\Models\Vital;
use App\Models\Clinical;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ViewWellnessReport extends Component
{
    public Registration $registration;
    public ?Nutrition $nutrition;
    public ?Vital $vital;
    public ?Clinical $clinical;
    public $pdfUrl;
    public $pdfFilename;

    public function downloadPdf()
    {
        if (empty($this->pdfFilename)) {
            $this->generatePdf();
        }

        $path = storage_path('app/public/pdfs/' . $this->pdfFilename);

        if (!file_exists($path)) {
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
        $this->clinical = $this->registration->clinicals()->first();

        // Configure DomPDF options
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', public_path()); // Allow access to public directory

        $pdf = Pdf::setOptions($options)
            ->loadView('livewire.wellness-report-pdf', [
                'record' => $this->registration->load(['goals']),
                'nutrition' => $this->nutrition,
                'vital' => $this->vital,
                'clinical' => $this->clinical,
                'currentDate' => now()->format('l, jS F Y'),
            ])
            ->setPaper('a4', 'portrait');

        $this->pdfFilename = 'wellness-report-' . $this->registration->id . '-' . now()->format('YmdHis') . '.pdf';
        $pdfPath = 'pdfs/' . $this->pdfFilename;

        Storage::disk('public')->put($pdfPath, $pdf->output());
        $this->pdfUrl = Storage::disk('public')->url($pdfPath);
    }

    public function mount(Registration $registration): void
    {
        $this->registration = $registration->load(['nutritions', 'vitals', 'clinicals', 'goals']);
        $this->nutrition = $this->registration->nutritions()->first();
        $this->vital = $this->registration->vitals()->first();
        $this->clinical = $this->registration->clinicals()->first();

        $this->generatePdf();
    }

    public function render()
    {
        return view('livewire.view-wellness-report');
    }
}