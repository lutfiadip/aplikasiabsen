<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn () => auth()->user()?->isAdmin()),
        ];
    }

    /**
     * Restrict listing for Pembimbing so they only see their mentees.
     */
    protected function getTableQuery(): ?Builder
    {
        $query = parent::getTableQuery();

        if (auth()->user()?->isPembimbing()) {
            return $query?->where('mentor_id', auth()->id());
        }

        return $query;
    }
}
