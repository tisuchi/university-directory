# US-047: Test ImportCommand — Basic Execution

## Story
As a package developer, I want feature tests that verify the `ud:import` command works end-to-end with mocked HTTP responses so that I can be confident the full import pipeline works.

## Prerequisites
- US-019 (ImportCommand exists)
- US-011 (DataClient exists)
- US-013 (UniversityImporter exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- Laravel Artisan testing (`$this->artisan()`)
- HTTP Client faking

## Implementation Checklist
- [ ] Create `tests/Feature/ImportCommandTest.php`
- [ ] Test: `imports single country` — Fake HTTP, run `ud:import DE`, assert universities exist in DB and command output contains success message
- [ ] Test: `imports multiple countries` — Run `ud:import DE US`, assert both countries' data is imported
- [ ] Test: `handles fetch failure gracefully` — Fake 500 response, run command, assert error output and command still exits
- [ ] Test: `displays summary output` — Run import, assert output contains "created" count

## Implementation Prompt
> Create `tests/Feature/ImportCommandTest.php` using Pest PHP closure syntax. Pest binds the TestCase via `tests/Pest.php`, so no class or `extends` is needed. Use `Http::fake()` to mock data source responses. `$this->artisan()` works inside Pest closures because `$this` refers to the TestCase instance. `test('imports single country', function () { ... })`: Fake response for DE.json with 3 sample universities. Run `$this->artisan('ud:import', ['countries' => ['DE']])`. Use `expect(University::count())->toBe(3)`. Assert command output contains expected text. `test('imports multiple countries', function () { ... })`: Fake DE.json and US.json. Run with both codes. Assert both countries' records exist. `test('handles fetch failure gracefully', function () { ... })`: Fake 500 for XX.json. Run `ud:import XX`. Assert command exits without crashing, output contains error message. `test('displays summary output', function () { ... })`: Run import, assert output contains 'created'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Feature/ImportCommandTest.php`
- [ ] Single country import works end-to-end
- [ ] Multiple country import works
- [ ] Fetch failures are handled gracefully (no crash)
- [ ] Command output contains summary stats
- [ ] All HTTP calls are mocked

## File(s) to Create/Modify
- `tests/Feature/ImportCommandTest.php` (create)
