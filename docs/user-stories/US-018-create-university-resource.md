# US-018: Create UniversityResource

## Story
As a developer, I want a Laravel API Resource for the University model so that I can return consistent, well-structured JSON responses from my API endpoints.

## Prerequisites
- US-006 (University model exists)

## Stack
- Laravel JSON Resources (`Illuminate\Http\Resources\Json\JsonResource`)
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Http/Resources/UniversityResource.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Http\Resources`
- [ ] Extend `JsonResource`
- [ ] Override `toArray($request)` method
- [ ] Return these fields: id, name, short_name, slug, country_code, type, aliases, official_website
- [ ] Cast `type` to its string value (enum → string)

## Implementation Prompt
> Create `src/Http/Resources/UniversityResource.php` in namespace `Tisuchi\UniversityDirectory\Http\Resources`. Extend `JsonResource`. In `toArray($request)`, return: id, name, short_name, slug, country_code, type (as string value from enum), aliases, official_website. Do not include latitude, longitude, created_at, or updated_at — those are internal fields.

## Acceptance Criteria
- [ ] File exists at `src/Http/Resources/UniversityResource.php`
- [ ] Extends `Illuminate\Http\Resources\Json\JsonResource`
- [ ] Returns exactly these fields: id, name, short_name, slug, country_code, type, aliases, official_website
- [ ] `type` field outputs the string value (e.g., "university"), not the enum object
- [ ] Does NOT include latitude, longitude, timestamps
- [ ] Can be used as `UniversityResource::collection($universities)`

## File(s) to Create/Modify
- `src/Http/Resources/UniversityResource.php` (create)
