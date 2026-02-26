# US-040: Test Basic Slug Generation

## Story
As a package developer, I want tests that verify basic slug generation from university names produces clean, URL-friendly slugs.

## Prerequisites
- US-015 (slug collision handling in importer exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory
- Laravel `Str::slug()`

## Implementation Checklist
- [ ] Create `tests/Unit/SlugCollisionTest.php`
- [ ] Extend base TestCase
- [ ] Test: `test_generates_basic_slug()` — "Technical University of Munich" → "technical-university-of-munich"
- [ ] Test: `test_generates_slug_with_special_characters()` — "Universitat Munchen" → "universitat-munchen"
- [ ] Test: `test_generates_slug_with_ampersand()` — "Arts & Sciences University" → "arts-sciences-university"

## Implementation Prompt
> Create `tests/Unit/SlugCollisionTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Unit`. Extend base TestCase. For these tests, instantiate the UniversityImporter and use reflection or make the slug method accessible. Alternatively, test through the import method by verifying the slug on created records. `test_generates_basic_slug`: import a record with name 'Technical University of Munich', assert the created record's slug is 'technical-university-of-munich'. `test_handles_special_characters`: name 'Universitat Munchen', assert slug is 'universitat-munchen'. `test_handles_ampersand`: name 'Arts & Sciences University', assert slug is 'arts-sciences-university'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/SlugCollisionTest.php`
- [ ] Basic ASCII names produce correct slugs
- [ ] Special characters (umlauts, accents) are transliterated
- [ ] Ampersands and symbols are handled
- [ ] All slugs are lowercase with hyphens

## File(s) to Create/Modify
- `tests/Unit/SlugCollisionTest.php` (create)
