<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VitalResource\Pages;
use App\Models\Vital;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VitalResource extends Resource
{
    protected static ?string $model = Vital::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Checkup';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Vitals')
                ->schema([
                    Forms\Components\TextInput::make('bp_systolic')->numeric(),
                    Forms\Components\TextInput::make('bp_diastolic')->numeric(),
                    Forms\Components\TextInput::make('pulse')->numeric(),
                    Forms\Components\TextInput::make('temp')->numeric(),
                    Forms\Components\TextInput::make('rbs')->numeric()->label('Random Blood Sugar'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bp_systolic'),
                Tables\Columns\TextColumn::make('bp_diastolic'),
                Tables\Columns\TextColumn::make('pulse'),
                Tables\Columns\TextColumn::make('temp'),
                Tables\Columns\TextColumn::make('rbs'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVitals::route('/'),
            'create' => Pages\CreateVital::route('/create'),
            'edit' => Pages\EditVital::route('/{record}/edit'),
        ];
    }
}
