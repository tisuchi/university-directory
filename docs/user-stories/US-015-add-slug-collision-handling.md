# US-015: Add Slug Collision Handling to UniversityImporter

## Story
As a package developer, I want slug collisions handled automatically during import so that every university gets a globally unique, URL-friendly slug.

## Prerequisites
- US-013 (UniversityImporter exists)
- US-006 (University model exists)

## Stack
- Laravel `Str::slug()`
- Eloquent queries for collision detection
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Services/UniversityImporter.php`
- [ ] Extract slug generation into a private method: `generateUniqueSlug(string $name, string $countryCode): string`
- [ ] Step 1: Try `Str::slug($name)` → e.g., `national-university`
- [ ] Step 2: If slug exists in DB, append country code → `national-university-ph`
- [ ] Step 3: If still exists, append incrementing numeric suffix → `national-university-ph-2`, `national-university-ph-3`...
- [ ] Query `University::where('slug', $slug)->exists()` for collision detection
- [ ] Replace the inline `Str::slug()` call in `import()` with `$this->generateUniqueSlug()`

## Implementation Prompt
> Add a private method `generateUniqueSlug(string $name, string $countryCode): string` to `src/Services/UniversityImporter.php`. Logic: 1) `$slug = Str::slug($name)`. 2) If `University::where('slug', $slug)->exists()`, append country code: `$slug = $slug . '-' . strtolower($countryCode)`. 3) If still exists, loop with incrementing suffix: `$slug . '-' . $i` starting from 2. Return the first non-colliding slug. Update the `import()` method to use `$this->generateUniqueSlug($item['name'], $countryCode)` instead of `Str::slug($item['name'])`.

## Acceptance Criteria
- [ ] `generateUniqueSlug()` private method exists
- [ ] First attempt: plain `Str::slug($name)` → `national-university`
- [ ] On collision: appends lowercase country code → `national-university-ph`
- [ ] On double collision: appends numeric suffix → `national-university-ph-2`
- [ ] Checks database for existing slugs using `University::where('slug', ...)->exists()`
- [ ] Import method uses `generateUniqueSlug()` for all slug generation
- [ ] Slugs are always lowercase and URL-friendly

## File(s) to Create/Modify
- `src/Services/UniversityImporter.php` (modify)
