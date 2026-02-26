<?php

namespace Tisuchi\UniversityDirectory\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tisuchi\UniversityDirectory\UniversityDirectoryServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            UniversityDirectoryServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
