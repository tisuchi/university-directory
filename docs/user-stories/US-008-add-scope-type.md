# US-008: Add scopeType to University Model

## Story
As a developer, I want to filter universities by type using an Eloquent scope so that I can write `University::type(UniversityType::College)->get()`.

## Prerequisites
- US-006 (University model exists)
- US-004 (UniversityType enum exists)

## Stack
- Laravel Eloquent Query Scopes
- PHP 8.2+ Enums

## Implementation Checklist
- [ ] Open `src/Models/University.php`
- [ ] Add method `scopeType(Builder $query, UniversityType $type): Builder`
- [ ] Import `Tisuchi\UniversityDirectory\Enums\UniversityType` (should already be imported for casts)
- [ ] Filter by `type` column using `$type->value`

## Implementation Prompt
> Add a `scopeType` method to the University model at `src/Models/University.php`. Method signature: `scopeType(Builder $query, UniversityType $type): Builder`. It should filter using `$query->where('type', $type->value)`. The UniversityType enum import should already exist from the casts definition.

## Acceptance Criteria
- [ ] `scopeType` method exists on University model
- [ ] Accepts `UniversityType $type` parameter (enum, not string)
- [ ] Returns `Builder` instance
- [ ] Filters using `$query->where('type', $type->value)`
- [ ] Works with any UniversityType case (University, College, Institute, Academy)
- [ ] Usage: `University::type(UniversityType::College)->get()` compiles correctly

## File(s) to Create/Modify
- `src/Models/University.php` (modify)
