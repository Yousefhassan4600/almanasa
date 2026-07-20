<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\EnsureCurrentAccount;
use App\Http\Middleware\SetPanelLocale;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;

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
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->navigationGroups([
                'Users & Subscriptions' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Users & Subscriptions'))
                    ->icon(Heroicon::OutlinedUsers),
                'Project Data' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Project Data'))
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal),
                'Users & Accounts' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Users & Accounts'))
                    ->icon(Heroicon::OutlinedUsers),
                'Provider Setup' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Provider Setup'))
                    ->icon(Heroicon::OutlinedBookOpen),
                'Learning Content' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Learning Content'))
                    ->icon(Heroicon::OutlinedBookOpen),
                'Students & Families' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Students & Families'))
                    ->icon(Heroicon::OutlinedUserGroup),
                'Sales & Payments' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Sales & Payments'))
                    ->icon(Heroicon::OutlinedCreditCard),
                'Communication & Website' => NavigationGroup::make()
                    ->label(fn (): string => __('admin.navigation_groups.Communication & Website'))
                    ->icon(Heroicon::OutlinedChatBubbleLeftRight),
            ])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => Livewire::mount('admin.account-picker'),
            )
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
