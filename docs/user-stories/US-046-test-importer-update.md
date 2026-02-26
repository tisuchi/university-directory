# US-046: Test UniversityImporter — Update Existing Records

## Story
As a package developer, I want tests that verify the importer updates existing records matched by wikidata_id without creating duplicates.

## Prerequisites
- US-014 (upsert logic exists)
- US-045 (basic importer tests exist)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityImporterTest.php`
- [ ] Test: `test('updates existing by wikidata id', function () { ... })` — Create a university with wikidata_id Q49108, then import a record with same wikidata_id but different name. Assert name is updated, count is still 1.
- [ ] Test: `test('does not duplicate on reimport', function () { ... })` — Import same dataset twice, assert count equals dataset size (not doubled)
- [ ] Test: `test('skips update when no update flag', function () { ... })` — Import with updateExisting=false, assert existing records are not modified
- [ ] Test: `test('never matches null wikidata id', function () { ... })` — Create record with null wikidata_id, import another with null wikidata_id, assert 2 records exist (no matching)

## Implementation Prompt
> Add tests to `tests/Unit/UniversityImporterTest.php` using Pest closure syntax. `test('updates existing by wikidata id', function () { ... })`: Create a University via factory with wikidata_id 'Q49108' and name 'Old Name'. Import a record with same wikidata_id and name 'New Name'. Use `expect(University::count())->toBe(1)` and `expect(University::first()->name)->toBe('New Name')`. `test('does not duplicate on reimport', function () { ... })`: Import 3 records, then import same 3 again, use `expect(University::count())->toBe(3)`. `test('skips update when no update flag', function () { ... })`: create a uni, import with same wikidata_id but different name with $updateExisting=false. Use `expect($uni->fresh()->name)->toBe('Old Name')` and `expect($stats['skipped'])->toBe(1)`. `test('never matches null wikidata id', function () { ... })`: create uni with null wikidata_id, import another with null wikidata_id, use `expect(University::count())->toBe(2)`.

## Acceptance Criteria
- [ ] Existing records are updated by wikidata_id match
- [ ] No duplicates on re-import
- [ ] `updateExisting=false` skips updates
- [ ] Null wikidata_id records are never matched (always create new)
- [ ] Stats correctly report updated and skipped counts

## File(s) to Create/Modify
- `tests/Unit/UniversityImporterTest.php` (modify)
