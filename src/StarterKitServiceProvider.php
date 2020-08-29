<?php

namespace Xmen\StarterKit;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Xmen\StarterKit\Commands\StarterKitCommand;

class StarterKitServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mapRoutes();
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../config/starter-kit.php' => config_path('starter-kit.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => base_path('resources/views/vendor/starter-kit'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../public' => base_path('public/vendor/starter-kit'),
            ], 'assets');

            $this->publishes([
                __DIR__ . '/../resources/lang' => base_path('resources/lang'),
            ], 'lang');

            $migrationFileName = 'create_starter_kit_table.php';
            if (!$this->migrationFileExists($migrationFileName)) {
                $this->publishes([
                    __DIR__ . "/../database/migrations/{$migrationFileName}.stub" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $migrationFileName),
                ], 'migrations');
            }

            $this->commands([
                StarterKitCommand::class,
            ]);
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'starter-kit');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/starter-kit.php', 'starter-kit');
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path("migrations/*.php")) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }

    public function mapRoutes()
    {
        Route::middleware('web')
            ->namespace('Xmen\StarterKit\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }
}
