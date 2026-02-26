# US-042: Test Slug Collision — Numeric Suffix

## Story
As a package developer, I want tests that verify triple slug collisions are resolved with a numeric suffix so that even the country-code-suffixed slug collision is handled.

## Prerequisites
- US-015 (slug collision handling exists)
- US-041 (country suffix test exists for context)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/SlugCollisionTest.php`
- [ ] Test: `test_appends_numeric_suffix_on_double_collision()` — Create records with slugs "national-university" AND "national-university-ph", then import another "National University" for PH. Assert third gets slug "national-university-ph-2"
- [ ] Test: `test_increments_numeric_suffix()` — Create "national-university", "national-university-ph", "national-university-ph-2". Assert next gets "national-university-ph-3"

## Implementation Prompt
> Add tests to `tests/Unit/SlugCollisionTest.php`. `test_appends_numeric_suffix_on_double_collision`: Create two University records via factory: one with slug 'national-university', another with slug 'national-university-ph'. Import a new record with name 'National University' and country_code 'PH'. Assert the new record has slug 'national-university-ph-2'. `test_increments_numeric_suffix`: Create three records with slugs 'national-university', 'national-university-ph', 'national-university-ph-2'. Import another 'National University' for PH. Assert slug is 'national-university-ph-3'.

## Acceptance Criteria
- [ ] Double collision produces "slug-cc-2"
- [ ] Triple collision produces "slug-cc-3"
- [ ] Numeric suffix increments correctly
- [ ] Existing records are not modified

## File(s) to Create/Modify
- `tests/Unit/SlugCollisionTest.php` (modify)
