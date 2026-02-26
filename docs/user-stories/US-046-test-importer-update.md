# US-046: Test UniversityImporter — Update Existing Records

## Story
As a package developer, I want tests that verify the importer updates existing records matched by wikidata_id without creating duplicates.

## Prerequisites
- US-014 (upsert logic exists)
- US-045 (basic importer tests exist)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityImporterTest.php`
- [ ] Test: `test_updates_existing_by_wikidata_id()` — Create a university with wikidata_id Q49108, then import a record with same wikidata_id but different name. Assert name is updated, count is still 1.
- [ ] Test: `test_does_not_duplicate_on_reimport()` — Import same dataset twice, assert count equals dataset size (not doubled)
- [ ] Test: `test_skips_update_when_no_update_flag()` — Import with updateExisting=false, assert existing records are not modified
- [ ] Test: `test_never_matches_null_wikidata_id()` — Create record with null wikidata_id, import another with null wikidata_id, assert 2 records exist (no matching)

## Implementation Prompt
> Add tests to `tests/Unit/UniversityImporterTest.php`. `test_updates_existing_by_wikidata_id`: Create a University via factory with wikidata_id 'Q49108' and name 'Old Name'. Import a record with same wikidata_id and name 'New Name'. Assert `University::count()` is 1 and `University::first()->name` is 'New Name'. `test_does_not_duplicate_on_reimport`: Import 3 records, then import same 3 again, assert count is 3. `test_skips_update_when_no_update_flag`: create a uni, import with same wikidata_id but different name with $updateExisting=false. Assert name unchanged and stats['skipped'] is 1. `test_never_matches_null_wikidata_id`: create uni with null wikidata_id, import another with null wikidata_id, assert count is 2.

## Acceptance Criteria
- [ ] Existing records are updated by wikidata_id match
- [ ] No duplicates on re-import
- [ ] `updateExisting=false` skips updates
- [ ] Null wikidata_id records are never matched (always create new)
- [ ] Stats correctly report updated and skipped counts

## File(s) to Create/Modify
- `tests/Unit/UniversityImporterTest.php` (modify)
