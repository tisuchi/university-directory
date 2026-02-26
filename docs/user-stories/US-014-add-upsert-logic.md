# US-014: Add Upsert Logic to UniversityImporter

## Story
As a package developer, I want the importer to update existing records (matched by wikidata_id) instead of creating duplicates so that re-imports refresh data without duplication.

## Prerequisites
- US-013 (UniversityImporter with basic insert exists)

## Stack
- Laravel Eloquent `updateOrCreate()` or manual find-then-update
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Services/UniversityImporter.php`
- [ ] Modify the `import()` method loop
- [ ] For each item, check if `wikidata_id` is not null
- [ ] If `wikidata_id` exists: use `University::updateOrCreate(['wikidata_id' => $id], $attributes)`
- [ ] Track whether the record was newly created or updated (check `$model->wasRecentlyCreated`)
- [ ] If `wikidata_id` is null: create a new record (manual entries are never matched)
- [ ] Update the returned stats array: increment 'updated' when an existing record is updated
- [ ] Add support for `$updateExisting` boolean parameter (default true) — when false, skip updating existing records

## Implementation Prompt
> Modify `import()` in `src/Services/UniversityImporter.php`. Add a third parameter `bool $updateExisting = true`. In the loop: if `wikidata_id` is not null, use `University::updateOrCreate(['wikidata_id' => $wikidataId], $attributes)`. Check `$model->wasRecentlyCreated` to determine if it was an insert or update. If `$updateExisting` is false and the record already exists, skip it and increment 'skipped'. If `wikidata_id` is null, always create a new record. Return updated stats with correct created/updated/skipped counts.

## Acceptance Criteria
- [ ] Existing records with matching `wikidata_id` are updated, not duplicated
- [ ] New records (no matching `wikidata_id`) are created
- [ ] Records with null `wikidata_id` are always created (never matched)
- [ ] `wasRecentlyCreated` is used to distinguish create vs update
- [ ] Stats correctly count created, updated, and skipped records
- [ ] `$updateExisting = false` causes existing records to be skipped
- [ ] Only these fields are updated: name, short_name, official_website, latitude, longitude, type, aliases

## File(s) to Create/Modify
- `src/Services/UniversityImporter.php` (modify)
