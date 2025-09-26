<?php

namespace App\Filament\Resources\VitalResource\Pages;

use App\Filament\Resources\VitalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVitals extends ListRecords
{
    protected static string $resource = VitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
