<?php

namespace Zorb\Promocodes;

use Illuminate\Support\ServiceProvider;
use Zorb\Promocodes\Console\ClearPromocodes;
use Zorb\Promocodes\Console\GeneratePromocodes;

class PromocodesServiceProvider extends ServiceProvider
{
    //
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/promocodes.php' => config_path('promocodes.php'),
            ], 'config');

            $this->commands([
                GeneratePromocodes::class,
                ClearPromocodes::class,
            ]);
        }

        if (!class_exists('CreatePromocodesTable')) {
            $this->publishes([
                __DIR__ . '/../stubs/create_promocodes_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_promocodes_table.php'),
            ], 'migrations');
        }

        if (!class_exists('CreatePromocodeUserTable')) {
            $this->publishes([
                __DIR__ . '/../stubs/create_promocode_user_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_promocode_user_table.php'),
            ], 'migrations');
        }
    }

    //
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/promocodes.php', 'promocodes');
    }
}
