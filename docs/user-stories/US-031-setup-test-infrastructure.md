# US-031: Set Up Testing Infrastructure

## Story
As a package developer, I want Pest PHP configured with Orchestra Testbench so that I can run expressive, closure-based tests against a real Laravel application environment with SQLite.

## Prerequisites
- US-001 (composer.json with dev dependencies)
- US-002 (tests/ directory structure)
- US-003 (service provider exists)

## Stack
- Pest PHP 3
- Orchestra Testbench 9/10
- SQLite in-memory database

## Implementation Checklist
- [ ] Create `phpunit.xml` at package root (Pest uses PHPUnit's XML config under the hood)
- [ ] Configure testsuites for `tests/Unit` and `tests/Feature`
- [ ] Set up SQLite in-memory database via environment variables:
  - `DB_CONNECTION=sqlite`
  - `DB_DATABASE=:memory:`
- [ ] Create `tests/TestCase.php` base class
- [ ] Extend `Orchestra\Testbench\TestCase`
- [ ] Override `getPackageProviders()` to return `[UniversityDirectoryServiceProvider::class]`
- [ ] Override `defineDatabaseMigrations()` to run migrations with `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')`
- [ ] Create `tests/Pest.php` configuration file
- [ ] In `tests/Pest.php`, bind the base TestCase globally: `uses(Tests\TestCase::class)->in('Feature', 'Unit')`

## Implementation Prompt
> Create three files for the test infrastructure:
> 1. `phpunit.xml` at package root: Configure with testsuites for `tests/Unit` and `tests/Feature`. Set environment variables: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`. Set colors to true.
> 2. `tests/TestCase.php`: Create a base test class in namespace `Tisuchi\UniversityDirectory\Tests`. Extend `Orchestra\Testbench\TestCase`. Override `getPackageProviders($app)` to return `[UniversityDirectoryServiceProvider::class]`. Override `defineDatabaseMigrations()` to call `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')`. Import the service provider class.
> 3. `tests/Pest.php`: Create the Pest configuration file. Add `uses(Tisuchi\UniversityDirectory\Tests\TestCase::class)->in('Feature', 'Unit');` to bind the base TestCase to all tests.

## Acceptance Criteria
- [ ] `phpunit.xml` exists at package root
- [ ] Testsuites configured for Unit and Feature directories
- [ ] SQLite in-memory database is configured
- [ ] `tests/TestCase.php` exists and extends Orchestra Testbench's TestCase
- [ ] `tests/Pest.php` exists with global `uses()` binding
- [ ] Package service provider is registered in test environment
- [ ] Migrations run automatically before each test
- [ ] Running `./vendor/bin/pest` from package root would execute tests

## File(s) to Create/Modify
- `phpunit.xml` (create)
- `tests/TestCase.php` (create)
- `tests/Pest.php` (create)
