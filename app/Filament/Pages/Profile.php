<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $slug = 'profile';
    protected static string $view = 'filament.pages.profile';

    // Make visible to all authenticated users
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    // Ensure the page is viewable regardless of Filament's global auth rules
    public static function canView(): bool
    {
        return true;
    }


}
