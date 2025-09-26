<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use App\Models\Vital;
use App\Models\Nutrition;
use App\Models\Clinical;
use App\Models\Corporate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Patient Management';
    protected static ?string $navigationLabel = 'Registrations';

    # ---------------- FORM ----------------
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Patient Demographics')
                    ->description('Basic patient information')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')->label('First Name')->required()->maxLength(100),
                                Forms\Components\TextInput::make('middle_name')->label('Middle Name')->maxLength(100),
                                Forms\Components\TextInput::make('surname')->label('Last Name')->required()->maxLength(100),
                                Forms\Components\TextInput::make('age')->label('Age')->numeric()->disabled()->dehydrated(true),
                                Forms\Components\TextInput::make('phone')->label('Phone Number')->tel()->maxLength(20)->unique(ignoreRecord: true)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        if (!empty($state) && Registration::where('phone', $state)->exists()) {
                                            Notification::make()
                                                ->title('Duplicate Phone Number')
                                                ->body("The phone number \"{$state}\" already exists.")
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                                Forms\Components\TextInput::make('email')->label('Email Address')->email()->maxLength(150)->unique(ignoreRecord: true)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state) {
                                        if (!empty($state) && Registration::where('email', $state)->exists()) {
                                            Notification::make()
                                                ->title('Duplicate Email')
                                                ->body("The email \"{$state}\" already exists.")
                                                ->danger()
                                                ->send();
                                        }
                                    }),
                            ]),
                        Forms\Components\Select::make('sex')
                            ->label('Gender')
                            ->options(['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other'])
                            ->required(),
                        Forms\Components\DatePicker::make('dob')
                            ->label('Date of Birth')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $state ? $set('age', Carbon::parse($state)->age) : null),
                        Forms\Components\Select::make('corporate_id')
                            ->label('Corporate/Organization')
                            ->relationship('corporate', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->label('Corporate Name')->maxLength(255),
                            ])
                            ->createOptionAction(fn (\Filament\Forms\Components\Actions\Action $action) =>
                                $action->modalHeading('Create Corporate')->modalSubmitActionLabel('Create')->modalWidth('lg')
                            ),
                        Forms\Components\Hidden::make('user_id')->default(fn () => auth()->id()),
                    ])
                    ->collapsible(),
            ]);
    }

    # ---------------- TABLE ----------------
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Patient Name')
                    ->getStateUsing(fn ($record) => trim("{$record->first_name} {$record->surname}"))
                    ->sortable()->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('corporate.name')->label('Organization')->sortable()->searchable()->badge(),
                Tables\Columns\TextColumn::make('age')->label('Age')->sortable()->badge()->color('primary'),
            ])
            ->actions([
                # -------- VITALS --------
                Action::make('vitals')
                    ->label(fn ($record) => $record->vitals()->exists() ? 'Edit Vitals' : 'Add Vitals')
                    ->color('success')
                    ->icon('heroicon-o-heart')
                    ->form([
                        Forms\Components\Section::make('Patient Summary')->description('Current patient information')->icon('heroicon-o-identification')->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('first_name')->label('First Name')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('middle_name')->label('Middle Name')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('surname')->label('Last Name')->disabled()->inlineLabel(),
                                ]),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('age')->label('Age')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('phone')->label('Phone')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('email')->label('Email')->disabled()->inlineLabel(),
                                ]),
                            ]),
                        ])->collapsible(),
                        Forms\Components\Section::make('Vital Signs')->description('Current physiological measurements')->icon('heroicon-o-chart-bar')->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('bp_systolic')->label('BP Systolic')->numeric()->suffix('mmHg')->required()->inlineLabel(),
                                    Forms\Components\TextInput::make('bp_diastolic')->label('BP Diastolic')->numeric()->suffix('mmHg')->required()->inlineLabel(),
                                    Forms\Components\TextInput::make('pulse')->label('Pulse Rate')->numeric()->suffix('bpm')->required()->inlineLabel(),
                                ]),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('temp')->label('Temperature')->numeric()->suffix('°C')->required()->inlineLabel(),
                                    Forms\Components\TextInput::make('rbs')->label('Random Blood Sugar')->numeric()->suffix('mg/dL')->required()->inlineLabel(),
                                ]),
                            ]),
                        ])->collapsible(),
                    ])
                    ->fillForm(fn ($record) => array_merge(
                        $record->vitals()->exists() ? $record->vitals()->first()->toArray() : [],
                        ['first_name'=>$record->first_name,'middle_name'=>$record->middle_name,'surname'=>$record->surname,'age'=>$record->age,'phone'=>$record->phone,'email'=>$record->email]
                    ))
                    ->action(function (array $data, $record) {
                        $data['registration_id'] = $record->id;
                        $data['user_id'] = auth()->id();
                        unset($data['first_name'],$data['middle_name'],$data['surname'],$data['age'],$data['phone'],$data['email']);
                        if ($record->vitals()->exists()) {
                            $record->vitals()->update($data);
                        } else {
                            Vital::create($data);
                        }
                    }),

                # -------- NUTRITION --------
                Action::make('nutrition')
                    ->label(fn ($record) => $record->vitals()->exists() ? ($record->nutritions()->exists() ? 'Edit Nutrition' : 'Add Nutrition') : 'Locked')
                    ->color('warning')
                    ->icon('heroicon-o-cake')
                    ->disabled(fn ($record) => ! $record->vitals()->exists())
                    ->form([
                        Forms\Components\Section::make('Patient Summary')->description('Current patient information')->icon('heroicon-o-identification')->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('first_name')->label('First Name')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('middle_name')->label('Middle Name')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('surname')->label('Last Name')->disabled()->inlineLabel(),
                                ]),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('age')->label('Age')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('phone')->label('Phone')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('email')->label('Email')->disabled()->inlineLabel(),
                                ]),
                            ]),
                        ])->collapsible(),
                        Forms\Components\Section::make('Latest Vitals')->description('Recent physiological measurements')->icon('heroicon-o-heart')->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('bp_systolic')->label('BP Systolic')->disabled()->suffix('mmHg')->inlineLabel(),
                                    Forms\Components\TextInput::make('bp_diastolic')->label('BP Diastolic')->disabled()->suffix('mmHg')->inlineLabel(),
                                    Forms\Components\TextInput::make('pulse')->label('Pulse Rate')->disabled()->suffix('bpm')->inlineLabel(),
                                ]),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('temp')->label('Temperature')->disabled()->suffix('°C')->inlineLabel(),
                                    Forms\Components\TextInput::make('rbs')->label('Random Blood Sugar')->disabled()->suffix('mg/dL')->inlineLabel(),
                                ]),
                            ]),
                        ])->collapsible(),
                        Forms\Components\Section::make('Nutritional Assessment')->description('Body composition and nutrition notes')->icon('heroicon-o-scale')->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('height')->label('Height (cm)')->numeric()->required()->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, $get) {
                                            $weight = $get('weight') ?? 0;
                                            if ($state && $weight) {
                                                $heightInMeters = $state / 100;
                                                $bmi = round($weight / ($heightInMeters ** 2), 1);
                                                $set('bmi', $bmi);
                                                $set('lower_limit_weight', round(18.5 * ($heightInMeters ** 2), 1));
                                                $set('weight_limit_weight', round(25 * ($heightInMeters ** 2), 1));
                                            }
                                        }),
                                    Forms\Components\TextInput::make('weight')->label('Weight (kg)')->numeric()->required()->reactive()
                                        ->afterStateUpdated(function ($state, callable $set, $get) {
                                            $height = $get('height') ?? 0;
                                            if ($state && $height) {
                                                $heightInMeters = $height / 100;
                                                $bmi = round($state / ($heightInMeters ** 2), 1);
                                                $set('bmi', $bmi);
                                                $set('lower_limit_weight', round(18.5 * ($heightInMeters ** 2), 1));
                                                $set('weight_limit_weight', round(25 * ($heightInMeters ** 2), 1));
                                            }
                                        }),
                                    Forms\Components\TextInput::make('bmi')->label('BMI')->numeric()->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('visceral_fat')->label('Visceral Fat')->numeric()->suffix('level')->minValue(1)->maxValue(59)->inlineLabel(),
                                ]),
                                Forms\Components\Grid::make(1)->schema([
                                    Forms\Components\TextInput::make('body_fat_percent')->label('Body Fat %')->numeric()->suffix('%')->minValue(1)->maxValue(100)->inlineLabel(),
                                    Forms\Components\TextInput::make('lower_limit_weight')->label('Lower Weight Limit')->numeric()->suffix('kg')->disabled()->inlineLabel(),
                                    Forms\Components\TextInput::make('weight_limit_weight')->label('Upper Weight Limit')->numeric()->suffix('kg')->disabled()->inlineLabel(),
                                ]),
                            ]),
                            Forms\Components\Textarea::make('notes_nutritionist')->label('Nutritionist Notes')->rows(4)->placeholder('Enter detailed nutrition assessment...')->columnSpanFull(),
                        ])->collapsible(),
                    ])
                    ->fillForm(fn ($record) => array_merge(
                        $record->nutritions()->exists() ? $record->nutritions()->first()->toArray() : [],
                        $record->vitals()->exists() ? $record->vitals()->first()->toArray() : [],
                        ['first_name'=>$record->first_name,'middle_name'=>$record->middle_name,'surname'=>$record->surname,'age'=>$record->age,'phone'=>$record->phone,'email'=>$record->email]
                    ))
                    ->action(function (array $data, $record) {
                        $data['registration_id'] = $record->id;
                        $data['user_id'] = auth()->id();
                        unset($data['first_name'],$data['middle_name'],$data['surname'],$data['age'],$data['phone'],$data['email']);
                        unset($data['bp_systolic'],$data['bp_diastolic'],$data['pulse'],$data['temp'],$data['rbs']);
                        unset($data['bmi'],$data['lower_limit_weight'],$data['weight_limit_weight']);
                        if ($record->nutritions()->exists()) {
                            $record->nutritions()->update($data);
                        } else {
                            Nutrition::create($data);
                        }
                    }),

                # -------- CLINICAL --------
                Action::make('clinical')
                    ->label(fn ($record) => $record->vitals()->exists() ? ($record->clinicals()->exists() ? 'Edit Clinical' : 'Add Clinical') : 'Locked')
                    ->color('info')
                    ->icon('heroicon-o-document-text')
                    ->disabled(fn ($record) => ! $record->vitals()->exists())
                    ->form([
                        Forms\Components\Section::make('Patient Summary')
                            ->description('Current patient information')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('first_name')->label('First Name')->disabled()->inlineLabel(),
                                        Forms\Components\TextInput::make('middle_name')->label('Middle Name')->disabled()->inlineLabel(),
                                        Forms\Components\TextInput::make('surname')->label('Last Name')->disabled()->inlineLabel(),
                                    ]),
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('age')->label('Age')->disabled()->inlineLabel(),
                                        Forms\Components\TextInput::make('phone')->label('Phone')->disabled()->inlineLabel(),
                                        Forms\Components\TextInput::make('email')->label('Email')->disabled()->inlineLabel(),
                                    ]),
                                ]),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Vitals Summary')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('bp_systolic')->label('BP Systolic')->disabled()->suffix('mmHg')->inlineLabel(),
                                        Forms\Components\TextInput::make('bp_diastolic')->label('BP Diastolic')->disabled()->suffix('mmHg')->inlineLabel(),
                                        Forms\Components\TextInput::make('pulse')->label('Pulse Rate')->disabled()->suffix('bpm')->inlineLabel(),
                                        Forms\Components\TextInput::make('temp')->label('Temperature')->disabled()->suffix('°C')->inlineLabel(),
                                        Forms\Components\TextInput::make('rbs')->label('Random Blood Sugar')->disabled()->suffix('mg/dL')->inlineLabel(),
                                    ]),
                                ]),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Nutrition Summary')
                            ->icon('heroicon-o-scale')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('height')->label('Height')->disabled()->suffix('cm')->inlineLabel(),
                                        Forms\Components\TextInput::make('weight')->label('Weight')->disabled()->suffix('kg')->inlineLabel(),
                                        Forms\Components\TextInput::make('bmi')->label('BMI')->disabled()->inlineLabel(),
                                        Forms\Components\TextInput::make('visceral_fat')->label('Visceral Fat')->disabled()->suffix('level')->inlineLabel(),
                                    ]),
                                    Forms\Components\Grid::make(1)->schema([
                                        Forms\Components\TextInput::make('body_fat_percent')->label('Body Fat %')->disabled()->suffix('%')->inlineLabel(),
                                        Forms\Components\TextInput::make('lower_limit_weight')->label('Lower Weight Limit')->disabled()->suffix('kg')->inlineLabel(),
                                        Forms\Components\TextInput::make('weight_limit_weight')->label('Upper Weight Limit')->disabled()->suffix('kg')->inlineLabel(),
                                    ]),
                                ]),
                                Forms\Components\Textarea::make('notes_nutritionist')->label('Nutritionist Notes')->disabled()->rows(4)->placeholder('No nutritionist notes available...')->columnSpanFull(),
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make('Clinical Assessment')
                            ->description('Professional medical evaluations')
                            ->icon('heroicon-o-clipboard-document')
                            ->schema([
                                Forms\Components\Textarea::make('notes_psychologist')->label('Psychology Notes')->rows(5)->placeholder('Psychological assessment and observations...')->columnSpanFull(),
                                Forms\Components\Textarea::make('notes_doctor')->label('Medical Notes')->rows(5)->placeholder('Medical diagnosis, treatment plan, and recommendations...')->columnSpanFull(),
                            ])
                            ->collapsible(),
                    ])
                    ->fillForm(fn ($record) => array_merge(
                        $record->clinicals()->exists() ? $record->clinicals()->first()->toArray() : [],
                        $record->nutritions()->exists() ? $record->nutritions()->first()->toArray() : [],
                        $record->vitals()->exists() ? $record->vitals()->first()->toArray() : [],
                        ['first_name'=>$record->first_name,'middle_name'=>$record->middle_name,'surname'=>$record->surname,'age'=>$record->age,'phone'=>$record->phone,'email'=>$record->email]
                    ))
                    ->action(function (array $data, $record) {
                        $data['registration_id'] = $record->id;
                        $data['user_id'] = auth()->id();
                        unset($data['first_name'],$data['middle_name'],$data['surname'],$data['age'],$data['phone'],$data['email']);
                        unset($data['bp_systolic'],$data['bp_diastolic'],$data['pulse'],$data['temp'],$data['rbs']);
                        unset($data['height'],$data['weight'],$data['bmi'],$data['lower_limit_weight'],$data['weight_limit_weight'],$data['visceral_fat'],$data['body_fat_percent'],$data['notes_nutritionist']);
                        if ($record->clinicals()->exists()) {
                            $record->clinicals()->update($data);
                        } else {
                            Clinical::create($data);
                        }
                    }),

                # -------- VIEW REPORT --------
                # -------- VIEW REPORT --------
Action::make('viewReport')
    ->label('View Report')
    ->color('warning')
    ->icon('heroicon-o-eye')
    ->modalHeading(fn($record) => 'Wellness Report - ' . $record->first_name . ' ' . $record->surname)
    ->modalContent(function (Registration $record) {
        // Generate PDF content directly in the modal
        $vital = $record->vitals()->first();
        $nutrition = $record->nutritions()->first();
        $currentDate = now()->format('F j, Y');
        
        return view('pdf.wellness-report', compact('record', 'vital', 'nutrition', 'currentDate'));
    })
    ->modalSubmitActionLabel('Download PDF')
    ->action(function (Registration $record) {
        // Download PDF action
        return response()->streamDownload(function () use ($record) {
            $vital = $record->vitals()->first();
            $nutrition = $record->nutritions()->first();
            $currentDate = now()->format('F j, Y');
            
            $html = view('pdf.wellness-report', compact('record', 'vital', 'nutrition', 'currentDate'))->render();
            
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            echo $dompdf->output();
        }, 'wellness-report-' . $record->id . '.pdf');
    }),

                # -------- DELETE --------
                DeleteAction::make()->icon('heroicon-o-trash')->color('danger'),
            ])
            ->bulkActions([
                BulkAction::make('delete')->label('Delete Selected')->color('danger')->icon('heroicon-o-trash')->requiresConfirmation()->action(fn (Collection $records) => $records->each->delete()),
            ]);
    }

    # ---------------- RELATIONS ----------------
    public static function getRelations(): array
    {
        return [];
    }

    # ---------------- PAGES ----------------
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}