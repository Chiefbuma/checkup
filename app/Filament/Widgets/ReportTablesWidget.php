<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Carbon\Carbon;

class ReportTablesWidget extends ApexChartWidget
{
    protected static ?int $sort = 1;
    protected static ?string $chartId = 'screeningSummaryChart';
    protected static ?string $heading = 'Screening Summary';

    protected array $ageRanges = [];
    protected array $genderCounts = [];
    protected array $bloodPressureSummary = [];
    protected array $bmiSummary = [];
    protected array $bodyTempSummary = [];
    protected array $bodyComposition = [];
    protected array $bloodSugarSummary = [];

    protected function getSeries(): array
    {
        $registrations = Registration::with(['vitals', 'nutritions'])->get();

        // 🔹 Age Ranges (10-year bins)
        $ages = $registrations->pluck('age')->toArray();
        $bins = range(0, 100, 10);
        $ageCounts = [];
        foreach ($bins as $start) {
            $end = $start + 9;
            $ageCounts["$start-$end"] = count(array_filter($ages, fn($age) => $age >= $start && $age <= $end));
        }
        $this->ageRanges = $ageCounts;

        // 🔹 Gender Counts
        $this->genderCounts = [
            'Male' => $registrations->where('sex', 'Male')->count(),
            'Female' => $registrations->where('sex', 'Female')->count(),
        ];

        // 🔹 Blood Pressure (latest per registration)
        $bpCounts = ['Normal'=>0,'Elevated'=>0,'Concerning'=>0];
        foreach ($registrations as $reg) {
            $vital = $reg->vitals->sortByDesc('created_at')->first();
            if ($vital && $vital->bp_systolic && $vital->bp_diastolic) {
                $s = $vital->bp_systolic;
                $d = $vital->bp_diastolic;
                if ($s < 120 && $d < 80) $bpCounts['Normal']++;
                elseif (($s >= 120 && $s < 130) && $d < 80) $bpCounts['Elevated']++;
                else $bpCounts['Concerning']++;
            }
        }
        $this->bloodPressureSummary = $bpCounts;

        // 🔹 BMI (latest per registration)
        $bmiCounts = ['Normal'=>0,'Overweight'=>0,'Obese'=>0];
        foreach ($registrations as $reg) {
            $nut = $reg->nutritions->sortByDesc('created_at')->first();
            if ($nut && $nut->bmi) {
                if ($nut->bmi < 25) $bmiCounts['Normal']++;
                elseif ($nut->bmi < 30) $bmiCounts['Overweight']++;
                else $bmiCounts['Obese']++;
            }
        }
        $this->bmiSummary = $bmiCounts;

        // 🔹 Body Temperature (latest per registration)
        $tempCounts = ['Normal'=>0,'Fever'=>0];
        foreach ($registrations as $reg) {
            $vital = $reg->vitals->sortByDesc('created_at')->first();
            if ($vital && $vital->temp) {
                $tempCounts[$vital->temp > 37.5 ? 'Fever' : 'Normal']++;
            }
        }
        $this->bodyTempSummary = $tempCounts;

        // 🔹 Body Composition (average body fat per gender)
        $femaleFat = $registrations
            ->filter(fn($r)=>$r->sex==='Female')
            ->flatMap(fn($r)=>$r->nutritions)
            ->whereNotNull('body_fat_percent')
            ->avg('body_fat_percent') ?? 0;

        $maleFat = $registrations
            ->filter(fn($r)=>$r->sex==='Male')
            ->flatMap(fn($r)=>$r->nutritions)
            ->whereNotNull('body_fat_percent')
            ->avg('body_fat_percent') ?? 0;

        $this->bodyComposition = ['Female Avg'=>$femaleFat,'Male Avg'=>$maleFat];

        // 🔹 Blood Sugar (RBS from latest vitals)
        $rbs = $registrations->flatMap(fn($r)=>$r->vitals)->whereNotNull('rbs');
        $avgRBS = $rbs->avg('rbs') ?? 0;
        $highCount = $rbs->whereBetween('rbs',[141,200])->count();
        $alarmingCount = $rbs->where('rbs','>',200)->count();
        $this->bloodSugarSummary = ['Average'=>$avgRBS,'High'=>$highCount,'Alarming'=>$alarmingCount];

        return []; // Charts are handled in the footer
    }

    protected function getFooter(): ?string
    {
        $ageRows = implode('', array_map(fn($range, $count) => "<tr><td>$range</td><td style='text-align:center;'>$count</td></tr>", array_keys($this->ageRanges), $this->ageRanges));
        $genderRows = implode('', array_map(fn($g, $c) => "<tr><td>$g</td><td style='text-align:center;'>$c</td></tr>", array_keys($this->genderCounts), $this->genderCounts));
        $bpRows = implode('', array_map(fn($s, $c) => "<tr><td>$s</td><td style='text-align:center;'>$c</td></tr>", array_keys($this->bloodPressureSummary), $this->bloodPressureSummary));
        $bmiRows = implode('', array_map(fn($s, $c) => "<tr><td>$s</td><td style='text-align:center;'>$c</td></tr>", array_keys($this->bmiSummary), $this->bmiSummary));
        $tempRows = implode('', array_map(fn($s, $c) => "<tr><td>$s</td><td style='text-align:center;'>$c</td></tr>", array_keys($this->bodyTempSummary), $this->bodyTempSummary));
        $bodyCompRows = implode('', array_map(fn($g, $c) => "<tr><td>$g</td><td style='text-align:center;'>".round($c,2)."%</td></tr>", array_keys($this->bodyComposition), $this->bodyComposition));
        $bloodSugarRows = implode('', array_map(fn($s, $v) => "<tr><td>$s</td><td style='text-align:center;'>".round($v,2)."</td></tr>", array_keys($this->bloodSugarSummary), $this->bloodSugarSummary));

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

<!-- Age Range -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Age Range Distribution</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Range</th><th>Count</th></tr></thead><tbody>{$ageRows}</tbody></table></div>
</div>

<!-- Gender Breakdown -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Gender Breakdown</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Gender</th><th>Count</th></tr></thead><tbody>{$genderRows}</tbody></table></div>
</div>

<!-- Blood Pressure -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Blood Pressure</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>{$bpRows}</tbody></table></div>
</div>

<!-- BMI -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">BMI Status</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>{$bmiRows}</tbody></table></div>
</div>

<!-- Body Temperature -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Body Temperature</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Status</th><th>Count</th></tr></thead><tbody>{$tempRows}</tbody></table></div>
</div>

<!-- Body Composition -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Body Composition</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Gender</th><th>Average Body Fat</th></tr></thead><tbody>{$bodyCompRows}</tbody></table></div>
</div>

<!-- Blood Sugar -->
<div class="table-container">
    <div class="title-container"><h2 style="color:white;">Blood Sugar</h2></div>
    <div class="table-wrapper"><table><thead><tr><th>Status</th><th>Value</th></tr></thead><tbody>{$bloodSugarRows}</tbody></table></div>
</div>
HTML;
    }
}
