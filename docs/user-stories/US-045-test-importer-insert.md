# US-045: Test UniversityImporter — Insert New Records

## Story
As a package developer, I want tests that verify the importer correctly creates new university records in the database.

## Prerequisites
- US-013 (UniversityImporter exists)
- US-014 (upsert logic exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityImporterTest.php`
- [ ] Test: `test('inserts new universities', function () { ... })` — Import 3 universities, assert 3 records in database
- [ ] Test: `test('sets correct attributes', function () { ... })` — Import one university, verify all attributes are set correctly
- [ ] Test: `test('returns correct created count', function () { ... })` — Import 5 records, assert stats['created'] is 5
- [ ] Test: `test('generates slug from name', function () { ... })` — Import "Technical University of Munich", assert slug is "technical-university-of-munich"

## Implementation Prompt
> Create `tests/Unit/UniversityImporterTest.php` using Pest closure syntax. The TestCase is bound via `tests/Pest.php` — no class or extends needed. Create a helper function that returns sample university data arrays. `test('inserts new universities', function () { ... })`: create importer, import 3 sample universities for 'DE', use `expect(University::count())->toBe(3)`. `test('sets correct attributes', function () { ... })`: import one uni with known data, fetch from DB, use `expect($uni->name)->toBe(...)`, `expect($uni->wikidata_id)->toBe(...)`, `expect($uni->country_code)->toBe(...)`, `expect($uni->slug)->toBe(...)`. `test('returns correct created count', function () { ... })`: import 5, use `expect($stats['created'])->toBe(5)` and `expect($stats['updated'])->toBe(0)`. `test('generates slug from name', function () { ... })`: import with name 'Technical University of Munich', use `expect($uni->slug)->toBe('technical-university-of-munich')`.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/UniversityImporterTest.php`
- [ ] New records are created in database
- [ ] All attributes are set correctly
- [ ] Stats correctly report created count
- [ ] Slugs are generated from names
- [ ] Tests use SQLite in-memory (no persistent state)

## File(s) to Create/Modify
- `tests/Unit/UniversityImporterTest.php` (create)
