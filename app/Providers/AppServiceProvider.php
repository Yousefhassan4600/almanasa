<?php

namespace App\Providers;

use App\Http\Middleware\SetWebsiteLocale;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app('translator')->addJsonPath(lang_path('website'));
        Livewire::addPersistentMiddleware([SetWebsiteLocale::class]);

        Event::listen([
            'eloquent.created: *',
            'eloquent.updated: *',
            'eloquent.deleted: *',
            'eloquent.forceDeleted: *',
            'eloquent.restored: *',
        ], function (string $eventName, array $data): void {
            $model = $data[0] ?? null;

            if (! $model instanceof Model) {
                return;
            }

            app(AuditLogger::class)->record(
                str($eventName)->between('eloquent.', ':')->toString(),
                $model,
            );
        });
    }
}
