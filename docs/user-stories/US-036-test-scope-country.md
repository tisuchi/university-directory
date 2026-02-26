# US-036: Test scopeCountry

## Story
As a package developer, I want tests that verify the country scope filters universities correctly by ISO country code.

## Prerequisites
- US-007 (scopeCountry exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add test closures to `tests/Unit/UniversityTest.php`
- [ ] Add test closure: `test('scope country filters by code', ...)` — create universities with DE and US, assert `University::country('DE')` returns only German ones
- [ ] Add test closure: `test('scope country is case insensitive', ...)` — `University::country('de')` should work (auto-uppercased)
- [ ] Add test closure: `test('scope country returns empty for no matches', ...)` — `University::country('XX')` returns empty collection

## Implementation Prompt
> Add test closures to `tests/Unit/UniversityTest.php` using Pest syntax. `test('scope country filters by code', function () { University::factory()->count(3)->create(['country_code' => 'DE']); University::factory()->count(2)->create(['country_code' => 'US']); expect(University::country('DE')->count())->toBe(3); });` — `test('scope country is case insensitive', function () { ... expect(University::country('de')->count())->toBe(3); });` — `test('scope country returns empty for no matches', function () { expect(University::country('XX')->count())->toBe(0); });`

## Acceptance Criteria
- [ ] Country filter returns only matching universities
- [ ] Filter is case-insensitive ('de' works as 'DE')
- [ ] Non-matching codes return empty results
- [ ] Filter works correctly with other chained scopes

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
