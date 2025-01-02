<?php

namespace App\Filament\Resources;

use App\Enums\DateType;
use App\Filament\Resources\AgendaResource\Pages;
use App\Filament\Resources\AgendaResource\RelationManagers;
use App\Models\Agenda;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class AgendaResource extends Resource
{
    protected static ?string $model = Agenda::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('summary')
                    ->columnSpanFull()
                    ->label(__('Summary'))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->rows(5),
                Forms\Components\Select::make('type')
                    ->options(DateType::getTypes())
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label(__('Location')),
                Forms\Components\DateTimePicker::make('start')
                    ->label(__('Start date'))
//                    ->reactive()
//                    ->afterStateUpdated(function ($state, callable $set) {
//                        if ($state) {
//                            // Stel dezelfde datum in voor 'end', tijd blijft leeg
//                            $set('end', Carbon::parse($state)->format('Y-m-d') . ' 00:00:00');
//                        }
//                    }),
                    ->required()
                    ->format('d-m-Y H:i')
                    ->after(Carbon::now()),
                Forms\Components\DateTimePicker::make('end')
                    ->label(__('End date'))
                    ->required()
                    ->format('d-m-Y H:i')
                    ->after('start'),

                Repeater::make('attendees')
                    ->label(__('Attendees'))
                    ->schema([
                        Forms\Components\Select::make('attendees')
                            ->label(__('E-mail'))
                            ->options(function (callable $get) {
                                // Haal de huidige waarden van de repeater op
                                // $currentMembers = $get('attendees') ?? [];
                                // Haal alleen de gebruikers op die nog niet zijn geselecteerd
                                return User::query()
                                // ->whereNotIn('id', [$currentMembers])
                                    ->pluck('email', 'id');
                            })
                            ->searchable(), // Optioneel, handig voor grotere lijsten
                    ])
                    ->columnSpanFull()
                    ->createItemButtonLabel(__('Add attendee')), // Optioneel

                Forms\Components\Toggle::make('in_agenda')
                    ->label(__('In agenda'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('summary')
                    ->searchable()
                    ->label(__('Summary')),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('Location'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('start')
                    ->label('Start date')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->label('End date')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('get_duration_start_end')
                    ->label('Duration')
                    ->sortable(),
                Tables\Columns\IconColumn::make('in_agenda')
                    ->label(__('In agenda'))
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendas::route('/'),
            'create' => Pages\CreateAgenda::route('/create'),
            'edit' => Pages\EditAgenda::route('/{record}/edit'),
        ];
    }
}
