<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ProfileMenuInjector extends Widget
{
    protected static string $view = 'filament.widgets.profile-menu-injector';

    public static function canView(): bool
    {
        return auth()->check();
    }
}