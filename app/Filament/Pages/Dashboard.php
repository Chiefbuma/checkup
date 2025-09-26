<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Carbon\Carbon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = null;

    public function getHeading(): string
    {
        return '';
    }

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function filtersForm(Form $form): Form
    {
        // Get max date from your data (adjust this query as needed)
        $maxDate = \App\Models\Registration::max('created_at') ?: now();
        $maxDate = Carbon::parse($maxDate);

        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Placeholder::make('dateRangeDisplay')
                            ->content(function ($get) use ($maxDate) {
                                $start = $get('startDate') ? Carbon::parse($get('startDate')) : now()->startOfMonth();
                                $end = $get('endDate') ? Carbon::parse($get('endDate')) : $maxDate;

                                return 
                                    $start->format('M j') . " to " .
                                    $end->format('M j');
                            })
                            ->columnSpan(1)
                            ->extraAttributes([
                                'class' => 'self-center bg-gray-800 text-gray-200 px-4 py-2 rounded-lg font-medium',
                                'style' => 'min-height: 42px; display: flex; align-items: center; font-size: 20px; font-weight: 700; color: #FFA500;'
                            ]),

                        DatePicker::make('startDate')
                            ->label('Start Date')
                            ->extraAttributes([
                                'class' => 'self-center bg-gray-800 text-gray-200 px-4 py-2 rounded-lg font-medium',
                                'style' => 'min-height: 42px; display: flex; align-items: center; font-size: 20px; font-weight: 700; color: #FFA500;'
                            ])

                            ->default(now()->startOfMonth())
                            ->columnSpan(1),

                        DatePicker::make('endDate')
                            ->label('End Date')
                            ->extraAttributes([
                                'class' => 'self-center bg-gray-800 text-gray-200 px-4 py-2 rounded-lg font-medium',
                                'style' => 'min-height: 42px; display: flex; align-items: center; font-size: 20px; font-weight: 700; color: #FFA500;'
                            ])

                            ->default($maxDate)
                            ->columnSpan(1),
                    ])
                    ->columns(3)
                    ->extraAttributes([
                        'class' => 'bg-black p-4 rounded-lg',
                        'style' => '--tw-bg-opacity: 1; background-color: rgba(0, 0, 0, var(--tw-bg-opacity));'
                    ]),
            ]);
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
