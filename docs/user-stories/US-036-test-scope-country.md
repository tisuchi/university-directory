# US-036: Test scopeCountry

## Story
As a package developer, I want tests that verify the country scope filters universities correctly by ISO country code.

## Prerequisites
- US-007 (scopeCountry exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityTest.php`
- [ ] Test: `test_scope_country_filters_by_code()` — create universities with DE and US, assert `University::country('DE')` returns only German ones
- [ ] Test: `test_scope_country_is_case_insensitive()` — `University::country('de')` should work (auto-uppercased)
- [ ] Test: `test_scope_country_returns_empty_for_no_matches()` — `University::country('XX')` returns empty collection

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTest.php`. `test_scope_country_filters_by_code`: create 3 unis with country_code 'DE' and 2 with 'US', assert `University::country('DE')->count()` is 3. `test_scope_country_is_case_insensitive`: same data, assert `University::country('de')->count()` is 3. `test_scope_country_returns_empty_for_no_matches`: assert `University::country('XX')->count()` is 0.

## Acceptance Criteria
- [ ] Country filter returns only matching universities
- [ ] Filter is case-insensitive ('de' works as 'DE')
- [ ] Non-matching codes return empty results
- [ ] Filter works correctly with other chained scopes

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
