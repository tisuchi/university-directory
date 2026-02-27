<?php

namespace Tisuchi\UniversityDirectory;

use Illuminate\Support\ServiceProvider;
use Tisuchi\UniversityDirectory\Console\ImportCommand;
use Tisuchi\UniversityDirectory\Console\ListCommand;
use Tisuchi\UniversityDirectory\Console\StatsCommand;
use Tisuchi\UniversityDirectory\Console\SyncCommand;

class UniversityDirectoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCommand::class,
                ListCommand::class,
                StatsCommand::class,
                SyncCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'university-directory-migrations');
        }
    }
}
