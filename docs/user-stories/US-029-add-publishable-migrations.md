# US-029: Add Publishable Migrations

## Story
As a package developer, I want users to be able to publish the migration files to their own app so that they can customize the schema if needed.

## Prerequisites
- US-027 (migrations are loaded in service provider)
- US-005 (migration file exists)

## Stack
- Laravel Service Provider `publishes()`
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/UniversityDirectoryServiceProvider.php`
- [ ] In the `boot()` method, inside the `runningInConsole()` block, add migration publishing
- [ ] Use `$this->publishes()` with the `'migrations'` tag
- [ ] Map the package migration directory to the app's `database/migrations` directory
- [ ] Users can then run: `php artisan vendor:publish --tag=university-directory-migrations`

## Implementation Prompt
> Modify `src/UniversityDirectoryServiceProvider.php`. Inside the `if ($this->app->runningInConsole())` block in boot(), add: `$this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'university-directory-migrations');`. This lets users run `php artisan vendor:publish --tag=university-directory-migrations` to copy migrations into their app.

## Acceptance Criteria
- [ ] `publishes()` is called with the migrations directory
- [ ] Tag is `'university-directory-migrations'`
- [ ] Source path points to package's `database/migrations/`
- [ ] Destination is the app's `database_path('migrations')`
- [ ] Only runs when in console context
- [ ] `php artisan vendor:publish --tag=university-directory-migrations` works

## File(s) to Create/Modify
- `src/UniversityDirectoryServiceProvider.php` (modify)
