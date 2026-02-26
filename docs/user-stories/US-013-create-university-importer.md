# US-013: Create UniversityImporter — Basic Insert

## Story
As a package developer, I want an importer service that takes an array of university data and inserts new records into the database so that fetched data becomes queryable.

## Prerequisites
- US-006 (University model exists)
- US-004 (UniversityType enum exists)

## Stack
- Laravel Eloquent
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Services/UniversityImporter.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Services`
- [ ] Add method `import(array $universities, string $countryCode): array` that returns stats (created, updated, skipped counts)
- [ ] Loop through each university data item
- [ ] For each item, create a new University record using `University::create()`
- [ ] Map incoming data fields to model attributes:
  - `wikidata_id` from source data
  - `name` from source data
  - `short_name` from source data (nullable)
  - `slug` — generate using `Str::slug($name)` (basic, collision handling in US-015)
  - `country_code` from the `$countryCode` parameter
  - `type` — default to 'university' for now (mapping in US-016)
  - `official_website` from source data (nullable)
  - `aliases` from source data (nullable, already an array)
  - `latitude` from source data (nullable)
  - `longitude` from source data (nullable)
- [ ] Return an associative array: `['created' => $count, 'updated' => 0, 'skipped' => 0]`

## Implementation Prompt
> Create `src/Services/UniversityImporter.php` in namespace `Tisuchi\UniversityDirectory\Services`. Add an `import(array $universities, string $countryCode): array` method. Loop through each item in `$universities`, create a University record via `University::create()` mapping: wikidata_id, name, short_name, slug (via `Str::slug($item['name'])`), country_code (from param), type ('university' default), official_website, aliases, latitude, longitude. Return `['created' => $count, 'updated' => 0, 'skipped' => 0]`. Import Str, University model. Keep it simple — no upsert or collision handling yet.

## Acceptance Criteria
- [ ] File exists at `src/Services/UniversityImporter.php`
- [ ] Namespace is `Tisuchi\UniversityDirectory\Services`
- [ ] `import()` method accepts array of universities and country code string
- [ ] Creates University records for each item
- [ ] Generates slugs using `Str::slug()`
- [ ] Returns stats array with created/updated/skipped counts
- [ ] Does NOT handle slug collisions yet (that's US-015)
- [ ] Does NOT handle upserts yet (that's US-014)

## File(s) to Create/Modify
- `src/Services/UniversityImporter.php` (create)
