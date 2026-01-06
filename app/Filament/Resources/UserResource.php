<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser),

                Forms\Components\Select::make('role')
                    ->options([
                        \App\Models\User::ROLE_ADMIN => 'Admin',
                        \App\Models\User::ROLE_PEMBIMBING => 'Pembimbing',
                        \App\Models\User::ROLE_ANAK_MAGANG => 'Anak Magang',
                    ])
                    ->required()
                    ->default(\App\Models\User::ROLE_ANAK_MAGANG),

                // Assign a mentor only for anak magang
                Forms\Components\Select::make('mentor_id')
                    ->label('Pembimbing')
                    ->options(fn () => \App\Models\User::where('role', \App\Models\User::ROLE_PEMBIMBING)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->visible(fn ($get) => $get('role') === \App\Models\User::ROLE_ANAK_MAGANG),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Forms\Components\DatePicker::make('active_from')
                    ->label('Active from')
                    ->placeholder('Optional')
                    ->reactive(),

                Forms\Components\DatePicker::make('active_until')
                    ->label('Active until')
                    ->placeholder('Optional')
                    ->reactive()
                    ->rules(['after_or_equal:active_from']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        \App\Models\User::ROLE_ADMIN => 'Admin',
                        \App\Models\User::ROLE_PEMBIMBING => 'Pembimbing',
                        \App\Models\User::ROLE_ANAK_MAGANG => 'Anak Magang',
                        default => (string) $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active')->sortable(),
                Tables\Columns\TextColumn::make('mentor.name')->label('Pembimbing')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('active_from')
                    ->date()
                    ->label('Active from')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('active_until')
                    ->date()
                    ->label('Active until')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email_verified_at')->dateTime(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () => auth()->user()?->isAdmin()),

                Tables\Actions\Action::make('toggleActive')
                    ->label(fn ($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn ($record) => $record->is_active ? 'heroicon-s-x-mark' : 'heroicon-s-check')
                    ->requiresConfirmation()
                    ->action(fn (\App\Models\User $record) => $record->update(['is_active' => ! $record->is_active]))
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),

                Tables\Actions\BulkAction::make('activate')
                    ->label('Activate selected')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $records->each->update(['is_active' => true]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->isAdmin()),

                Tables\Actions\BulkAction::make('deactivate')
                    ->label('Deactivate selected')
                    ->action(function (\Illuminate\Support\Collection $records) {
                        $records->each->update(['is_active' => false]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->isAdmin()),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
