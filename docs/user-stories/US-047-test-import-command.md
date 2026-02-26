# US-047: Test ImportCommand — Basic Execution

## Story
As a package developer, I want feature tests that verify the `ud:import` command works end-to-end with mocked HTTP responses so that I can be confident the full import pipeline works.

## Prerequisites
- US-019 (ImportCommand exists)
- US-011 (DataClient exists)
- US-013 (UniversityImporter exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- Laravel Artisan testing (`$this->artisan()`)
- HTTP Client faking

## Implementation Checklist
- [ ] Create `tests/Feature/ImportCommandTest.php`
- [ ] Extend base TestCase
- [ ] Test: `test_imports_single_country()` — Fake HTTP, run `ud:import DE`, assert universities exist in DB and command output contains success message
- [ ] Test: `test_imports_multiple_countries()` — Run `ud:import DE US`, assert both countries' data is imported
- [ ] Test: `test_handles_fetch_failure_gracefully()` — Fake 500 response, run command, assert error output and command still exits
- [ ] Test: `test_displays_summary_output()` — Run import, assert output contains "created" count

## Implementation Prompt
> Create `tests/Feature/ImportCommandTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Feature`. Extend base TestCase. Use `Http::fake()` to mock data source responses. `test_imports_single_country`: Fake response for DE.json with 3 sample universities. Run `$this->artisan('ud:import', ['countries' => ['DE']])`. Assert `University::count()` is 3. Assert command output contains expected text. `test_imports_multiple_countries`: Fake DE.json and US.json. Run with both codes. Assert both countries' records exist. `test_handles_fetch_failure_gracefully`: Fake 500 for XX.json. Run `ud:import XX`. Assert command exits without crashing, output contains error message. `test_displays_summary_output`: Run import, assert output contains 'created'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Feature/ImportCommandTest.php`
- [ ] Single country import works end-to-end
- [ ] Multiple country import works
- [ ] Fetch failures are handled gracefully (no crash)
- [ ] Command output contains summary stats
- [ ] All HTTP calls are mocked

## File(s) to Create/Modify
- `tests/Feature/ImportCommandTest.php` (create)
