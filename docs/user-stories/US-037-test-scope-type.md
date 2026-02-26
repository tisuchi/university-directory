# US-037: Test scopeType

## Story
As a package developer, I want tests that verify the type scope filters universities correctly by UniversityType enum.

## Prerequisites
- US-008 (scopeType exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Add tests to `tests/Unit/UniversityTest.php`
- [ ] Test: `test_scope_type_filters_by_enum()` — create universities of different types, assert filtering by College returns only colleges
- [ ] Test: `test_scope_type_with_university()` — filter for University type
- [ ] Test: `test_scope_type_returns_empty_for_no_matches()` — filter for a type with no records

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTest.php`. `test_scope_type_filters_by_enum`: create 2 unis with type 'university' and 3 with type 'college', assert `University::type(UniversityType::College)->count()` is 3. `test_scope_type_with_university_type`: same data, assert `University::type(UniversityType::University)->count()` is 2. `test_scope_type_returns_empty_when_no_matches`: assert `University::type(UniversityType::Academy)->count()` is 0.

## Acceptance Criteria
- [ ] Type filter returns only universities matching the given enum case
- [ ] Works for all UniversityType cases
- [ ] Returns empty when no records match
- [ ] Accepts UniversityType enum (not raw string)

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (modify)
