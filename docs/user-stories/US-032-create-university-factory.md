# US-032: Create University Model Factory

## Story
As a package developer, I want a model factory for University so that tests can easily create test data with realistic defaults.

## Prerequisites
- US-006 (University model exists)
- US-004 (UniversityType enum exists)
- US-031 (test infrastructure exists)

## Stack
- Laravel Model Factories
- Faker
- PHP 8.2+

## Implementation Checklist
- [ ] Create `database/factories/UniversityFactory.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Database\Factories`
- [ ] Extend `Illuminate\Database\Eloquent\Factories\Factory`
- [ ] Set `protected $model = University::class`
- [ ] Define `definition()` returning:
  - `wikidata_id` → `'Q' . $this->faker->unique()->numberBetween(1000, 999999)`
  - `name` → `$this->faker->company() . ' University'`
  - `short_name` → `strtoupper($this->faker->lexify('???'))` (3-letter abbreviation)
  - `slug` → generated from name via `Str::slug()`
  - `country_code` → `$this->faker->randomElement(['DE', 'US', 'GB', 'FR', 'JP'])`
  - `type` → `$this->faker->randomElement(UniversityType::cases())->value`
  - `official_website` → `$this->faker->url()`
  - `aliases` → `[$this->faker->company()]`
  - `latitude` → `$this->faker->latitude()`
  - `longitude` → `$this->faker->longitude()`
- [ ] Add `HasFactory` trait to University model
- [ ] Override `newFactory()` in University model to return the correct factory class (needed for package factories)

## Implementation Prompt
> Create two changes:
> 1. Create `database/factories/UniversityFactory.php` in namespace `Tisuchi\UniversityDirectory\Database\Factories`. Extend Factory. Set $model to University::class. In definition(), return realistic fake data: wikidata_id as 'Q' + random number, name as company + ' University', short_name as 3 uppercase letters, slug from Str::slug of name, country_code from ['DE','US','GB','FR','JP'], type from random UniversityType case value, official_website as URL, aliases as array with one fake company name, latitude and longitude from faker.
> 2. Modify `src/Models/University.php`: Add `use Illuminate\Database\Eloquent\Factories\HasFactory` trait. Add a `protected static function newFactory()` method that returns `\Tisuchi\UniversityDirectory\Database\Factories\UniversityFactory::new()`.

## Acceptance Criteria
- [ ] Factory file exists at `database/factories/UniversityFactory.php`
- [ ] Factory generates valid University data
- [ ] `wikidata_id` follows pattern `Q{number}`
- [ ] `slug` is generated from `name` using `Str::slug()`
- [ ] `type` uses valid UniversityType enum values
- [ ] `aliases` is an array (not null)
- [ ] University model has `HasFactory` trait
- [ ] `University::factory()->create()` works in tests
- [ ] `University::factory()->count(10)->create()` works for bulk creation

## File(s) to Create/Modify
- `database/factories/UniversityFactory.php` (create)
- `src/Models/University.php` (modify — add HasFactory trait and newFactory method)
