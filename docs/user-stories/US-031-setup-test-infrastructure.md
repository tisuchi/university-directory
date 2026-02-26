# US-031: Set Up Testing Infrastructure

## Story
As a package developer, I want PHPUnit configured with Orchestra Testbench so that I can run tests against a real Laravel application environment with SQLite.

## Prerequisites
- US-001 (composer.json with dev dependencies)
- US-002 (tests/ directory structure)
- US-003 (service provider exists)

## Stack
- PHPUnit 11
- Orchestra Testbench 9/10
- SQLite in-memory database

## Implementation Checklist
- [ ] Create `phpunit.xml` at package root
- [ ] Configure PHPUnit to use `tests/` directory
- [ ] Set up SQLite in-memory database via environment variables:
  - `DB_CONNECTION=sqlite`
  - `DB_DATABASE=:memory:`
- [ ] Create `tests/TestCase.php` base class
- [ ] Extend `Orchestra\Testbench\TestCase`
- [ ] Override `getPackageProviders()` to return `[UniversityDirectoryServiceProvider::class]`
- [ ] Override `defineDatabaseMigrations()` to run migrations with `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')`
- [ ] All test classes will extend this base TestCase

## Implementation Prompt
> Create two files for the test infrastructure:
> 1. `phpunit.xml` at package root: Configure PHPUnit 11 with testsuites for `tests/Unit` and `tests/Feature`. Set environment variables: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`. Set colors to true.
> 2. `tests/TestCase.php`: Create a base test class in namespace `Tisuchi\UniversityDirectory\Tests`. Extend `Orchestra\Testbench\TestCase`. Override `getPackageProviders($app)` to return `[UniversityDirectoryServiceProvider::class]`. Override `defineDatabaseMigrations()` to call `$this->loadMigrationsFrom(__DIR__ . '/../database/migrations')`. Import the service provider class.

## Acceptance Criteria
- [ ] `phpunit.xml` exists at package root
- [ ] PHPUnit is configured with Unit and Feature testsuites
- [ ] SQLite in-memory database is configured
- [ ] `tests/TestCase.php` exists and extends Orchestra Testbench's TestCase
- [ ] Package service provider is registered in test environment
- [ ] Migrations run automatically before each test
- [ ] Running `./vendor/bin/phpunit` from package root would execute tests

## File(s) to Create/Modify
- `phpunit.xml` (create)
- `tests/TestCase.php` (create)
