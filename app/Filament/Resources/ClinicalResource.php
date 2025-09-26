<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClinicalResource\Pages;
use App\Models\Clinical;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClinicalResource extends Resource
{
    protected static ?string $model = Clinical::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Checkup';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Clinical Notes')
                ->schema([
                    Forms\Components\Textarea::make('notes_psychologist')->label('Notes Psychologist'),
                    Forms\Components\Textarea::make('notes_doctor')->label('Notes Doctor'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('notes_psychologist'),
                Tables\Columns\TextColumn::make('notes_doctor'),
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
            'index' => Pages\ListClinicals::route('/'),
            'create' => Pages\CreateClinical::route('/create'),
            'edit' => Pages\EditClinical::route('/{record}/edit'),
        ];
    }
}
