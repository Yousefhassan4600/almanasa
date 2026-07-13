<?php

namespace App\Providers\Filament;

use App\Filament\Base\BaseResource;
use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\EnsureCurrentAccount;
use App\Http\Middleware\SetPanelLocale;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                NavigationGroup::make(BaseResource::PROJECT_DATA_NAVIGATION_GROUP)
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal),
                NavigationGroup::make('Identity & Accounts')
                    ->icon(Heroicon::OutlinedUsers),
                NavigationGroup::make('Education Setup')
                    ->icon(Heroicon::OutlinedBookOpen),
                NavigationGroup::make('Learning Content')
                    ->icon(Heroicon::OutlinedBookOpen),
                NavigationGroup::make('Operations')
                    ->icon(Heroicon::OutlinedCube),
                NavigationGroup::make('Communication & Website')
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight),
                NavigationGroup::make('Sales & Payments')
                    ->icon(Heroicon::OutlinedCreditCard),
                NavigationGroup::make('Students & Families')
                    ->icon(Heroicon::OutlinedUserGroup),
            ])
            ->navigationItems([
                NavigationItem::make(BaseResource::EDUCATION_CATALOG_NAVIGATION_PARENT)
                    ->group(BaseResource::PROJECT_DATA_NAVIGATION_GROUP)
                    ->icon(Heroicon::OutlinedAcademicCap)
                    ->sort(1),
                NavigationItem::make(BaseResource::LOCATIONS_NAVIGATION_PARENT)
                    ->group(BaseResource::PROJECT_DATA_NAVIGATION_GROUP)
                    ->icon(Heroicon::OutlinedMap)
                    ->sort(2),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->plugins([
                FilamentLanguageSwitcherPlugin::make()
                    ->rememberLocale()
                    ->locales([
                        ['code' => 'en', 'name' => 'English', 'flag' => 'us'],
                        ['code' => 'ar', 'name' => 'العربية', 'flag' => 'sa'],
                    ]),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetPanelLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureCurrentAccount::class.':dashboard',
            ])
            ->sidebarWidth(69)
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->font('Cairo');
    }
}
