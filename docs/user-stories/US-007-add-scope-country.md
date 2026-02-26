# US-007: Add scopeCountry to University Model

## Story
As a developer, I want to filter universities by country code using a clean Eloquent scope so that I can write `University::country('DE')->get()`.

## Prerequisites
- US-006 (University model exists)

## Stack
- Laravel Eloquent Query Scopes
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Models/University.php`
- [ ] Add method `scopeCountry(Builder $query, string $countryCode): Builder`
- [ ] Import `Illuminate\Database\Eloquent\Builder`
- [ ] Filter by `country_code` column using `where()`
- [ ] Convert input to uppercase for consistency (ISO 3166-1 alpha-2 is uppercase)

## Implementation Prompt
> Add a `scopeCountry` method to the University model at `src/Models/University.php`. Method signature: `scopeCountry(Builder $query, string $countryCode): Builder`. It should filter by the `country_code` column, converting the input to uppercase with `strtoupper()`. Import `Illuminate\Database\Eloquent\Builder` at the top.

## Acceptance Criteria
- [ ] `scopeCountry` method exists on University model
- [ ] Accepts a `string $countryCode` parameter
- [ ] Returns `Builder` instance
- [ ] Filters using `$query->where('country_code', strtoupper($countryCode))`
- [ ] `Builder` class is imported
- [ ] `University::country('de')->toSql()` would produce a query with `WHERE country_code = 'DE'`

## File(s) to Create/Modify
- `src/Models/University.php` (modify)
