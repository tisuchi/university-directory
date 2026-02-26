# US-040: Test Basic Slug Generation

## Story
As a package developer, I want tests that verify basic slug generation from university names produces clean, URL-friendly slugs.

## Prerequisites
- US-015 (slug collision handling in importer exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory
- Laravel `Str::slug()`

## Implementation Checklist
- [ ] Create `tests/Unit/SlugCollisionTest.php`
- [ ] Add test closure: `test('generates basic slug', ...)` — "Technical University of Munich" → "technical-university-of-munich"
- [ ] Add test closure: `test('generates slug with special characters', ...)` — "Universitat Munchen" → "universitat-munchen"
- [ ] Add test closure: `test('generates slug with ampersand', ...)` — "Arts & Sciences University" → "arts-sciences-university"

## Implementation Prompt
> Create `tests/Unit/SlugCollisionTest.php`. Use Pest closure syntax (no class needed — base TestCase is bound via `tests/Pest.php`). For these tests, instantiate the UniversityImporter and use reflection or make the slug method accessible. Alternatively, test through the import method by verifying the slug on created records. `test('generates basic slug', function () { /* import record with name 'Technical University of Munich' */ expect($record->slug)->toBe('technical-university-of-munich'); });` — `test('handles special characters', function () { /* name 'Universitat Munchen' */ expect($record->slug)->toBe('universitat-munchen'); });` — `test('handles ampersand', function () { /* name 'Arts & Sciences University' */ expect($record->slug)->toBe('arts-sciences-university'); });`

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/SlugCollisionTest.php`
- [ ] Basic ASCII names produce correct slugs
- [ ] Special characters (umlauts, accents) are transliterated
- [ ] Ampersands and symbols are handled
- [ ] All slugs are lowercase with hyphens

## File(s) to Create/Modify
- `tests/Unit/SlugCollisionTest.php` (create)
