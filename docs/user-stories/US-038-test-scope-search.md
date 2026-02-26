# US-038: Test scopeSearch (Name and Short Name)

## Story
As a package developer, I want tests that verify the search scope finds universities by partial name and short name matches.

## Prerequisites
- US-009 (scopeSearch exists with name/short_name)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add test closures to `tests/Unit/UniversityTest.php`
- [ ] Add test closure: `test('search by name', ...)` — create "Technical University of Munich", search "Munich", assert found
- [ ] Add test closure: `test('search by short name', ...)` — create with short_name "TUM", search "TUM", assert found
- [ ] Add test closure: `test('search partial match', ...)` — search "Tech" finds "Technical University of Munich"
- [ ] Add test closure: `test('search returns empty for no match', ...)` — search "XYZ123" returns empty
- [ ] Add test closure: `test('search with null term', ...)` — `University::search(null)` returns all records (no filter)

## Implementation Prompt
> Add test closures to `tests/Unit/UniversityTest.php` using Pest syntax. `test('search by name', function () { University::factory()->create(['name' => 'Technical University of Munich']); expect(University::search('Munich')->count())->toBe(1); });` — `test('search by short name', function () { University::factory()->create(['short_name' => 'TUM']); expect(University::search('TUM')->count())->toBe(1); });` — `test('search partial match', function () { ... expect(University::search('Tech')->count())->toBe(1); });` — `test('search returns empty for no match', function () { expect(University::search('XYZ123')->count())->toBe(0); });` — `test('search with null returns all', function () { University::factory()->count(3)->create(); expect(University::search(null)->count())->toBe(3); });`

## Acceptance Criteria
- [ ] Search by full name works
- [ ] Search by short_name works
- [ ] Partial name matches work
- [ ] Non-matching search returns empty
- [ ] Null/empty search term returns all records (no filter applied)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
