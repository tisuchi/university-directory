# US-037: Test scopeType

## Story
As a package developer, I want tests that verify the type scope filters universities correctly by UniversityType enum.

## Prerequisites
- US-008 (scopeType exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add test closures to `tests/Unit/UniversityTest.php`
- [ ] Add test closure: `test('scope type filters by enum', ...)` — create universities of different types, assert filtering by College returns only colleges
- [ ] Add test closure: `test('scope type with university', ...)` — filter for University type
- [ ] Add test closure: `test('scope type returns empty for no matches', ...)` — filter for a type with no records

## Implementation Prompt
> Add test closures to `tests/Unit/UniversityTest.php` using Pest syntax. `test('scope type filters by enum', function () { University::factory()->count(2)->create(['type' => 'university']); University::factory()->count(3)->create(['type' => 'college']); expect(University::type(UniversityType::College)->count())->toBe(3); });` — `test('scope type with university type', function () { ... expect(University::type(UniversityType::University)->count())->toBe(2); });` — `test('scope type returns empty when no matches', function () { expect(University::type(UniversityType::Academy)->count())->toBe(0); });`

## Acceptance Criteria
- [ ] Type filter returns only universities matching the given enum case
- [ ] Works for all UniversityType cases
- [ ] Returns empty when no records match
- [ ] Accepts UniversityType enum (not raw string)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
