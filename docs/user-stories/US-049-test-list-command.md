# US-049: Test ListCommand

## Story
As a package developer, I want feature tests that verify the `ud:list` command displays universities correctly with filters.

## Prerequisites
- US-024 (ListCommand exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- Laravel Artisan testing

## Implementation Checklist
- [ ] Create `tests/Feature/ListCommandTest.php`
- [ ] Test: `lists universities` — Create 5 universities via factory, run `ud:list`, assert output contains university names
- [ ] Test: `filters by country` — Create DE and US universities, run `ud:list --country=DE`, assert only German universities appear
- [ ] Test: `filters by search` — Create "Munich" university, run `ud:list --search=Munich`, assert it appears
- [ ] Test: `respects limit` — Create 30 universities, run `ud:list --limit=5`, assert only 5 shown
- [ ] Test: `shows empty message` — Empty database, run `ud:list`, assert appropriate output

## Implementation Prompt
> Create `tests/Feature/ListCommandTest.php` using Pest PHP closure syntax. Pest binds the TestCase via `tests/Pest.php`, so no class or `extends` is needed. `$this->artisan()` works inside Pest closures because `$this` refers to the TestCase instance. `test('lists universities', function () { ... })`: Create 5 universities via factory with known names. Run `$this->artisan('ud:list')` and assert output contains at least one university name. `test('filters by country', function () { ... })`: Create 3 with country_code 'DE' and 2 with 'US'. Run `$this->artisan('ud:list', ['--country' => 'DE'])`. Assert output contains German uni names. `test('filters by search', function () { ... })`: Create uni with name 'Technical University of Munich'. Run with --search=Munich. Assert output contains 'Munich'. `test('respects limit', function () { ... })`: Create 30 unis, run with --limit=5. `test('shows message when empty', function () { ... })`: No records, run ud:list, check output.

## Acceptance Criteria
- [ ] Test file exists at `tests/Feature/ListCommandTest.php`
- [ ] Universities are listed in table format
- [ ] `--country` filter works
- [ ] `--search` filter works
- [ ] `--limit` restricts results
- [ ] Empty database handled gracefully

## File(s) to Create/Modify
- `tests/Feature/ListCommandTest.php` (create)
