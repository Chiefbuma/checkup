<?php

namespace App\Filament\Resources\VitalResource\Pages;

use App\Filament\Resources\VitalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVital extends EditRecord
{
    protected static string $resource = VitalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
