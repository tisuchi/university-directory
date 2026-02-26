# US-006: Create University Model (Basic)

## Story
As a developer, I want a University Eloquent model with proper table configuration, fillable attributes, and casts so that I can interact with university data using Laravel conventions.

## Prerequisites
- US-004 (UniversityType enum exists)
- US-005 (migration exists to understand the schema)

## Stack
- Laravel Eloquent Model
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Models/University.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Models`
- [ ] Extend `Illuminate\Database\Eloquent\Model`
- [ ] Set `$table = 'ud_universities'`
- [ ] Define `$fillable` with all columns except `id`, `created_at`, `updated_at`
- [ ] Define `$casts` array:
  - `type` → `Tisuchi\UniversityDirectory\Enums\UniversityType`
  - `aliases` → `array`
  - `latitude` → `float`
  - `longitude` → `float`

## Implementation Prompt
> Create a Laravel Eloquent model at `src/Models/University.php` in namespace `Tisuchi\UniversityDirectory\Models`. Table: `ud_universities`. Fillable: wikidata_id, name, short_name, slug, country_code, type, official_website, aliases, latitude, longitude. Casts: type → UniversityType enum (from Tisuchi\UniversityDirectory\Enums), aliases → array, latitude → float, longitude → float. No scopes yet — just the basic model.

## Acceptance Criteria
- [ ] File exists at `src/Models/University.php`
- [ ] Namespace is `Tisuchi\UniversityDirectory\Models`
- [ ] Extends `Illuminate\Database\Eloquent\Model`
- [ ] `$table` is set to `'ud_universities'`
- [ ] `$fillable` contains exactly: wikidata_id, name, short_name, slug, country_code, type, official_website, aliases, latitude, longitude
- [ ] `$casts` maps `type` to `UniversityType::class`
- [ ] `$casts` maps `aliases` to `'array'`
- [ ] `$casts` maps `latitude` and `longitude` to `'float'`
- [ ] No scopes or relationships defined yet
- [ ] File is valid PHP

## File(s) to Create/Modify
- `src/Models/University.php` (create)
