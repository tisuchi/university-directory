# US-038: Test scopeSearch (Name and Short Name)

## Story
As a package developer, I want tests that verify the search scope finds universities by partial name and short name matches.

## Prerequisites
- US-009 (scopeSearch exists with name/short_name)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityTest.php`
- [ ] Test: `test_search_by_name()` — create "Technical University of Munich", search "Munich", assert found
- [ ] Test: `test_search_by_short_name()` — create with short_name "TUM", search "TUM", assert found
- [ ] Test: `test_search_partial_match()` — search "Tech" finds "Technical University of Munich"
- [ ] Test: `test_search_returns_empty_for_no_match()` — search "XYZ123" returns empty
- [ ] Test: `test_search_with_null_term()` — `University::search(null)` returns all records (no filter)

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTest.php`. `test_search_by_name`: create uni with name 'Technical University of Munich', assert `University::search('Munich')->count()` is 1. `test_search_by_short_name`: create uni with short_name 'TUM', assert `University::search('TUM')->count()` is 1. `test_search_partial_match`: search 'Tech' finds the Munich uni. `test_search_returns_empty_for_no_match`: search 'XYZ123' count is 0. `test_search_with_null_returns_all`: create 3 unis, assert `University::search(null)->count()` is 3.

## Acceptance Criteria
- [ ] Search by full name works
- [ ] Search by short_name works
- [ ] Partial name matches work
- [ ] Non-matching search returns empty
- [ ] Null/empty search term returns all records (no filter applied)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
