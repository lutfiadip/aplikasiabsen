<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Dotswan\MapPicker\Fields\Map;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id()),
                Forms\Components\Placeholder::make('user_name')
                    ->label('User')
                    ->content(fn() => auth()->user()?->name ?? 'N/A'),
                Forms\Components\Select::make('schedule_id')
                    ->relationship('schedule', 'name'),
                Forms\Components\DatePicker::make('attendance_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('check_in_time'),
                Forms\Components\DateTimePicker::make('check_out_time'),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->reactive(),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->reactive(),
                
                Map::make('location')
                    ->label('Location')
                    ->columnSpanFull()
                    ->defaultLocation(latitude: -7.050465229627423, longitude: 110.39243352297622)
                    ->draggable(true)
                    ->clickable(false) 
                    ->liveLocation(true, true, 5000) 
                    ->zoom(15)
                    ->minZoom(0)
                    ->maxZoom(28)
                    ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png")
                    ->detectRetina(true)
                    ->afterStateUpdated(function ($state, $set) {
                        $set('latitude', $state['lat']);
                        $set('longitude', $state['lng']);
                    })
                    ->afterStateHydrated(function ($state, $set, $record) {
                        if ($record) {
                            $set('location', [
                                'lat' => $record->latitude,
                                'lng' => $record->longitude,
                            ]);
                        }
                    }),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_late')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export to Excel')
                        ->icon('heroicon-s-document-arrow-down')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $ids = $records->pluck('id')->toArray();

                            return redirect()->route('attendances.export', ['ids' => $ids]);
                        }),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
