# US-039: Test scopeSearch (Aliases)

## Story
As a package developer, I want tests that verify the search scope also matches against JSON aliases so that searching "MIT" finds universities with that alias.

## Prerequisites
- US-010 (scopeSearch with alias support exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add test closures to `tests/Unit/UniversityTest.php`
- [ ] Add test closure: `test('search by alias', ...)` — create uni with name "Massachusetts Institute of Technology" and aliases `["MIT", "Mass Tech"]`, search "MIT", assert found
- [ ] Add test closure: `test('search alias partial match', ...)` — search "Mass" finds the uni via alias "Mass Tech"
- [ ] Add test closure: `test('search combines name and alias', ...)` — create 2 unis, one with name containing "MIT" and another with alias "MIT", search "MIT" finds both

## Implementation Prompt
> Add test closures to `tests/Unit/UniversityTest.php` using Pest syntax. `test('search by alias', function () { University::factory()->create(['name' => 'Massachusetts Institute of Technology', 'aliases' => ['MIT', 'Mass Tech']]); expect(University::search('MIT')->count())->toBe(1); });` — `test('search alias partial match', function () { ... expect(University::search('Mass')->count())->toBe(1); });` — `test('search combines name short name and alias', function () { University::factory()->create(['name' => 'MIT Academy']); University::factory()->create(['name' => 'Some University', 'aliases' => ['MIT']]); expect(University::search('MIT')->count())->toBe(2); });`

## Acceptance Criteria
- [ ] Search finds universities by alias value
- [ ] Partial alias matches work
- [ ] Search combines results from name, short_name, and aliases
- [ ] JSON array aliases like `["MIT", "Mass Tech"]` are searchable
- [ ] Works on SQLite (cross-database LIKE approach)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
