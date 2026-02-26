# US-050: Test StatsCommand

## Story
As a package developer, I want feature tests that verify the `ud:stats` command shows correct database statistics.

## Prerequisites
- US-025 (StatsCommand exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- Laravel Artisan testing

## Implementation Checklist
- [ ] Create or add to `tests/Feature/ListCommandTest.php` (or create `tests/Feature/StatsCommandTest.php`)
- [ ] Test: `shows total count` — Create 10 universities, run `ud:stats`, assert output contains "10"
- [ ] Test: `shows country count` — Create universities in 3 countries, assert output contains "3"
- [ ] Test: `shows type breakdown` — Create mix of types, assert output contains type names and counts
- [ ] Test: `handles empty database` — No records, run `ud:stats`, assert output shows "0" without errors

## Implementation Prompt
> Add stats command tests using Pest PHP closure syntax. Create `tests/Feature/StatsCommandTest.php`. Pest binds the TestCase via `tests/Pest.php`, so no class or `extends` is needed. `$this->artisan()` works inside Pest closures because `$this` refers to the TestCase instance. `test('shows total count', function () { ... })`: Create 10 universities via factory. Run `$this->artisan('ud:stats')`. Use `expect()` for assertions. Assert output contains '10'. `test('shows country count', function () { ... })`: Create unis in DE, US, GB (3 countries). Assert output contains '3'. `test('shows type breakdown', function () { ... })`: Create 5 with type 'university' and 3 with type 'college'. Assert output contains 'university' and 'college' with counts. `test('handles empty database', function () { ... })`: No records. Run ud:stats. Assert command succeeds and output contains '0'.

## Acceptance Criteria
- [ ] Stats command shows total university count
- [ ] Shows distinct country count
- [ ] Shows type breakdown with counts
- [ ] Empty database doesn't crash — shows zeros
- [ ] All numbers are accurate

## File(s) to Create/Modify
- `tests/Feature/StatsCommandTest.php` (create) or `tests/Feature/ListCommandTest.php` (modify)
