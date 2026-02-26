# US-041: Test Slug Collision — Country Code Suffix

## Story
As a package developer, I want tests that verify slug collisions are resolved by appending the country code so that duplicate names across countries get unique slugs.

## Prerequisites
- US-015 (slug collision handling exists)
- US-013 (UniversityImporter exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/SlugCollisionTest.php`
- [ ] Test: `test('appends country code on collision', function () { ... })` — Create a university with slug "national-university", then import another with same name but different country. Assert second gets slug "national-university-ph"
- [ ] Test: `test('country suffix is lowercase', function () { ... })` — Assert the suffix uses lowercase country code

## Implementation Prompt
> Add tests to `tests/Unit/SlugCollisionTest.php` using Pest closure syntax. `test('appends country code on collision', function () { ... })`: First, create a University record with slug 'national-university' (via factory with specific slug). Then use UniversityImporter to import a record with name 'National University' and country_code 'PH'. Use `expect($newRecord->slug)->toBe('national-university-ph')`. `test('country suffix is lowercase', function () { ... })`: Same scenario with country 'DE', use `expect($newRecord->slug)->toContain('-de')` and `expect($newRecord->slug)->not->toContain('-DE')`.

## Acceptance Criteria
- [ ] When slug "national-university" exists, next one becomes "national-university-ph"
- [ ] Country suffix is lowercase
- [ ] Original record's slug is unchanged
- [ ] Works with any country code

## File(s) to Create/Modify
- `tests/Unit/SlugCollisionTest.php` (modify)
