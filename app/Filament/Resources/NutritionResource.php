<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NutritionResource\Pages;
use App\Models\Nutrition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NutritionResource extends Resource
{
    protected static ?string $model = Nutrition::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';
    protected static ?string $navigationGroup = 'Checkup';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Nutrition')
                ->schema([
                    Forms\Components\TextInput::make('visceral_fat')->numeric(),
                    Forms\Components\TextInput::make('body_fat_percent')->numeric()->label('Body Fat %'),
                    Forms\Components\Textarea::make('notes_nutritionist')->label('Notes (Nutritionist)'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visceral_fat'),
                Tables\Columns\TextColumn::make('body_fat_percent'),
                Tables\Columns\TextColumn::make('notes_nutritionist'),
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
            'index' => Pages\ListNutrition::route('/'),
            'create' => Pages\CreateNutrition::route('/create'),
            'edit' => Pages\EditNutrition::route('/{record}/edit'),
        ];
    }
}
