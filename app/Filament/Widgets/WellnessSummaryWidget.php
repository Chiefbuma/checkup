<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CheckupSummaryWidget extends ApexChartWidget
{
    protected static ?int $sort = 1;
    protected static ?string $chartId = 'checkupSummaryChart';
    protected static ?string $heading = 'Checkup Statistics';

    protected array $cumulativeSummary = [];
    protected array $registrationsSummary = [];

    protected function getSeries(): array
    {
        // 🔹 Overall summary: total registrations, completed vitals, nutrition, clinical checkups
        $summary = Registration::query()
            ->withCount(['vitals', 'nutritions', 'clinicals'])
            ->get();

        $totalRegistrations = $summary->count();
        $totalVitals = $summary->sum('vitals_count');
        $totalNutritions = $summary->sum('nutritions_count');
        $totalClinicals = $summary->sum('clinicals_count');

        $this->cumulativeSummary = [[
            'registrations' => $totalRegistrations,
            'vitals' => $totalVitals,
            'nutrition' => $totalNutritions,
            'clinical' => $totalClinicals,
        ]];

        // 🔹 Latest 5 Registrations
        $registrations = Registration::query()
            ->withCount(['vitals', 'nutritions', 'clinicals'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $this->registrationsSummary = $registrations->map(function ($reg) {
            return [
                'id' => $reg->id,
                'name' => "{$reg->first_name} {$reg->surname}",
                'vitals' => $reg->vitals_count,
                'nutrition' => $reg->nutritions_count,
                'clinical' => $reg->clinicals_count,
            ];
        })->toArray();

        // 🔹 For radial chart, overall % of completed checkups
        $totalCheckups = $totalVitals + $totalNutritions + $totalClinicals;
        $possibleCheckups = $totalRegistrations * 3; // 3 types: vitals, nutrition, clinical
        $performance = $possibleCheckups > 0 ? round(($totalCheckups / $possibleCheckups) * 100, 2) : 0;

        return [$performance];
    }

    protected function getOptions(): array
    {
        return [
            'chart' => [
                'type' => 'radialBar',
                'height' => 400,
                'toolbar' => ['show' => false],
            ],
            'series' => $this->getSeries(),
            'plotOptions' => [
                'radialBar' => [
                    'startAngle' => -140,
                    'endAngle' => 130,
                    'hollow' => ['size' => '50%'],
                    'dataLabels' => [
                        'name' => ['show' => true, 'color' => 'orange', 'fontSize' => '16px'],
                        'value' => ['show' => true, 'color' => 'orange', 'fontSize' => '28px'],
                    ],
                ],
            ],
            'labels' => ['Checkup Completion %'],
            'colors' => ['#f59e0b'],
        ];
    }

    protected function getFooter(): ?string
    {
        // Cumulative Summary Table
        $cumulativeRows = '';
        if (!empty($this->cumulativeSummary)) {
            $record = $this->cumulativeSummary[0];
            $cumulativeRows = <<<HTML
<tr>
    <td style="text-align:center;">1</td>
    <td style="text-align:center;">{$record['registrations']}</td>
    <td style="text-align:center;">{$record['vitals']}</td>
    <td style="text-align:center;">{$record['nutrition']}</td>
    <td style="text-align:center;">{$record['clinical']}</td>
</tr>
HTML;
        }

        // Latest 5 Registrations
        $latestRows = '';
        foreach ($this->registrationsSummary as $reg) {
            $latestRows .= <<<HTML
<tr>
    <td style="text-align:center;">{$reg['id']}</td>
    <td style="text-align:left;">{$reg['name']}</td>
    <td style="text-align:center;">{$reg['vitals']}</td>
    <td style="text-align:center;">{$reg['nutrition']}</td>
    <td style="text-align:center;">{$reg['clinical']}</td>
</tr>
HTML;
        }

        return <<<HTML
<style>
@import url("https://fonts.googleapis.com/css2?family=Bai+Jamjuree:wght@200;300;400;500;600;700&display=swap");

.table-container { font-family: "Bai Jamjuree", sans-serif; background-color: black; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
.table-wrapper { max-height: 400px; overflow-y: auto; margin-bottom: 10px; &::-webkit-scrollbar { display: none; } -ms-overflow-style: none; scrollbar-width: none; }
table { width: 100%; border-collapse: collapse; color: white; }
th { position: sticky; top: 0; background-color: #1a1a1a; text-align: left; padding: 12px 10px; font-weight: 600; font-size: 16px; color: #f59e0b; border-bottom: 1px solid rgba(255, 255, 255, 0.2); z-index: 10; }
td { padding: 10px; text-align: left; font-size: 14px; }
tr:hover { background-color: rgba(255, 255, 255, 0.05); }
.title-container { text-align: left; font-size: 15px; padding: 12px; border-radius: 12px; width: 100%; background-color: rgba(255, 255, 255, 0.1); font-family: "Bai Jamjuree", sans-serif; margin-bottom: 16px; }
</style>

<div class="table-container">
    <div class="title-container">
        <h2 style="color: white; margin: 0;">Cumulative Checkups</h2>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Registrations</th>
                    <th>Vitals</th>
                    <th>Nutrition</th>
                    <th>Clinical</th>
                </tr>
            </thead>
            <tbody>
                {$cumulativeRows}
            </tbody>
        </table>
    </div>
</div>

<div class="table-container">
    <div class="title-container">
        <h2 style="color: white; margin: 0;">Latest 5 Registrations</h2>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Vitals</th>
                    <th>Nutrition</th>
                    <th>Clinical</th>
                </tr>
            </thead>
            <tbody>
                {$latestRows}
            </tbody>
        </table>
    </div>
</div>
HTML;
    }
}
