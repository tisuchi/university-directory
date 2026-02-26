# US-009: Add scopeSearch for Name and Short Name

## Story
As a developer, I want to search universities by name or short_name using an Eloquent scope so that I can write `University::search('Munich')->get()`.

## Prerequisites
- US-006 (University model exists)

## Stack
- Laravel Eloquent Query Scopes
- SQL LIKE queries

## Implementation Checklist
- [ ] Open `src/Models/University.php`
- [ ] Add method `scopeSearch(Builder $query, ?string $term): Builder`
- [ ] Return early (no filter) if `$term` is null or empty
- [ ] Use `where()` with `LIKE` on `name` column
- [ ] Use `orWhere()` with `LIKE` on `short_name` column
- [ ] Wrap search term with `%` wildcards for partial matching
- [ ] Group the OR conditions in a closure to prevent query leaks

## Implementation Prompt
> Add a `scopeSearch` method to the University model at `src/Models/University.php`. Signature: `scopeSearch(Builder $query, ?string $term): Builder`. If $term is null or empty, return $query unchanged. Otherwise, wrap the conditions in `$query->where(function ($q) use ($term) { ... })` and search `name` LIKE %term% OR `short_name` LIKE %term%. Use a grouped where to prevent OR leaks in chained queries.

## Acceptance Criteria
- [ ] `scopeSearch` method exists on University model
- [ ] Accepts nullable string `$term`
- [ ] Returns `Builder` unchanged if `$term` is null or empty string
- [ ] Searches `name` column with LIKE %term%
- [ ] Searches `short_name` column with LIKE %term%
- [ ] OR conditions are grouped inside a closure (`where(function ...)`)
- [ ] `University::search('MIT')->country('US')->get()` does not leak OR across country filter
- [ ] Partial matches work: searching "Mun" matches "Munich"

## File(s) to Create/Modify
- `src/Models/University.php` (modify)
