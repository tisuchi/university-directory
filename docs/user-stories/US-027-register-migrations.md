# US-027: Register Migrations in Service Provider

## Story
As a package developer, I want the service provider to load migrations automatically so that users can run `php artisan migrate` without manually copying migration files.

## Prerequisites
- US-003 (service provider shell exists)
- US-005 (migration file exists)

## Stack
- Laravel Service Provider `loadMigrationsFrom()`
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/UniversityDirectoryServiceProvider.php`
- [ ] In the `boot()` method, add `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')`
- [ ] Remove the TODO comment for migrations

## Implementation Prompt
> Modify `src/UniversityDirectoryServiceProvider.php`. In the `boot()` method, add `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');` to automatically load the package's migrations. Remove any TODO comment about migrations.

## Acceptance Criteria
- [ ] `loadMigrationsFrom()` is called in `boot()` with correct path
- [ ] Path resolves to `database/migrations/` relative to the package root
- [ ] Running `php artisan migrate` in a host app will pick up the package migration
- [ ] No TODO comments remain for migration loading

## File(s) to Create/Modify
- `src/UniversityDirectoryServiceProvider.php` (modify)
