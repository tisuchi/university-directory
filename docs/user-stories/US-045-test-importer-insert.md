# US-045: Test UniversityImporter — Insert New Records

## Story
As a package developer, I want tests that verify the importer correctly creates new university records in the database.

## Prerequisites
- US-013 (UniversityImporter exists)
- US-014 (upsert logic exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityImporterTest.php`
- [ ] Extend base TestCase
- [ ] Test: `test_inserts_new_universities()` — Import 3 universities, assert 3 records in database
- [ ] Test: `test_sets_correct_attributes()` — Import one university, verify all attributes are set correctly
- [ ] Test: `test_returns_correct_created_count()` — Import 5 records, assert stats['created'] is 5
- [ ] Test: `test_generates_slug_from_name()` — Import "Technical University of Munich", assert slug is "technical-university-of-munich"

## Implementation Prompt
> Create `tests/Unit/UniversityImporterTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Unit`. Extend base TestCase. Create a helper method that returns sample university data arrays. `test_inserts_new_universities`: create importer, import 3 sample universities for 'DE', assert `University::count()` is 3. `test_sets_correct_attributes`: import one uni with known data, fetch from DB, assert name, wikidata_id, country_code, slug match. `test_returns_correct_created_count`: import 5, assert `$stats['created']` is 5 and `$stats['updated']` is 0. `test_generates_slug_from_name`: import with name 'Technical University of Munich', assert slug is 'technical-university-of-munich'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/UniversityImporterTest.php`
- [ ] New records are created in database
- [ ] All attributes are set correctly
- [ ] Stats correctly report created count
- [ ] Slugs are generated from names
- [ ] Tests use SQLite in-memory (no persistent state)

## File(s) to Create/Modify
- `tests/Unit/UniversityImporterTest.php` (create)
