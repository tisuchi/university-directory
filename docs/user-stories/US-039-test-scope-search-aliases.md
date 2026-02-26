# US-039: Test scopeSearch (Aliases)

## Story
As a package developer, I want tests that verify the search scope also matches against JSON aliases so that searching "MIT" finds universities with that alias.

## Prerequisites
- US-010 (scopeSearch with alias support exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityTest.php`
- [ ] Test: `test_search_by_alias()` — create uni with name "Massachusetts Institute of Technology" and aliases `["MIT", "Mass Tech"]`, search "MIT", assert found
- [ ] Test: `test_search_alias_partial_match()` — search "Mass" finds the uni via alias "Mass Tech"
- [ ] Test: `test_search_combines_name_and_alias()` — create 2 unis, one with name containing "MIT" and another with alias "MIT", search "MIT" finds both

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTest.php`. `test_search_by_alias`: create uni with name 'Massachusetts Institute of Technology', aliases ['MIT', 'Mass Tech']. Assert `University::search('MIT')->count()` is 1. `test_search_alias_partial_match`: search 'Mass' finds the uni (via alias 'Mass Tech'). `test_search_combines_name_short_name_and_alias`: create uni A with name 'MIT Academy' (no aliases) and uni B with name 'Some University' aliases ['MIT']. Search 'MIT' should find both (count is 2).

## Acceptance Criteria
- [ ] Search finds universities by alias value
- [ ] Partial alias matches work
- [ ] Search combines results from name, short_name, and aliases
- [ ] JSON array aliases like `["MIT", "Mass Tech"]` are searchable
- [ ] Works on SQLite (cross-database LIKE approach)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
