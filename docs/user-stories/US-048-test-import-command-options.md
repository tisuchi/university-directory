# US-048: Test ImportCommand — Options (--no-update, --region, --all)

## Story
As a package developer, I want feature tests that verify the import command's options work correctly so that all command variations are tested.

## Prerequisites
- US-020 (--region flag exists)
- US-021 (--all flag exists)
- US-022 (--no-update exists)
- US-047 (basic import test exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- Laravel Artisan testing

## Implementation Checklist
- [ ] Add tests to `tests/Feature/ImportCommandTest.php`
- [ ] Test: `no-update flag skips existing` — Import data, then re-import with `--no-update`, assert records are not updated
- [ ] Test: `region flag imports region` — Run with `--region=europe`, assert command processes multiple European country codes (mock all of them)
- [ ] Test: `all flag imports everything` — Run with `--all`, assert command processes all available countries
- [ ] Test: `fails without countries or flags` — Run with no arguments and no flags, assert error output

## Implementation Prompt
> Add tests to `tests/Feature/ImportCommandTest.php` using Pest PHP closure syntax. `$this->artisan()` works inside Pest closures because `$this` refers to the TestCase instance. `test('no-update flag skips existing', function () { ... })`: Create a University with wikidata_id 'Q49108' and name 'Old Name'. Fake HTTP with same wikidata_id but 'New Name'. Run `$this->artisan('ud:import', ['countries' => ['DE'], '--no-update' => true])`. Use `expect($university->fresh()->name)->toBe('Old Name')`. `test('region flag imports region', function () { ... })`: Fake HTTP for multiple country JSON files. Run `$this->artisan('ud:import', ['--region' => 'europe'])`. Assert command was successful (exit code 0). `test('all flag with confirmation', function () { ... })`: Fake HTTP. Run with --all and --no-interaction flags. Assert command attempts to process countries. `test('fails without arguments', function () { ... })`: Run `$this->artisan('ud:import')` with no args. Assert exit code is not 0 or output contains error.

## Acceptance Criteria
- [ ] `--no-update` prevents existing records from being updated
- [ ] `--region` resolves to correct country codes
- [ ] `--all` processes all available countries
- [ ] Missing arguments/flags produces an error
- [ ] All options work with mocked HTTP

## File(s) to Create/Modify
- `tests/Feature/ImportCommandTest.php` (modify)
