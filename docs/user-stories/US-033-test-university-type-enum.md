# US-033: Test UniversityType Enum Values

## Story
As a package developer, I want tests that verify the UniversityType enum has the correct cases and values so that changes to the enum are caught immediately.

## Prerequisites
- US-004 (UniversityType enum exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- PHP 8.2+ Enums

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityTypeTest.php`
- [ ] Extend base TestCase
- [ ] Test that enum has exactly 4 cases
- [ ] Test each case has the correct string value:
  - `University` → `'university'`
  - `College` → `'college'`
  - `Institute` → `'institute'`
  - `Academy` → `'academy'`
- [ ] Test `UniversityType::from('university')` returns `University` case
- [ ] Test `UniversityType::tryFrom('invalid')` returns null

## Implementation Prompt
> Create `tests/Unit/UniversityTypeTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Unit`. Extend the base TestCase. Write tests: `test_has_exactly_four_cases()` — assert count of `UniversityType::cases()` is 4. `test_university_case_value()` — assert `UniversityType::University->value` is `'university'`. Similar for College, Institute, Academy. `test_can_create_from_value()` — assert `UniversityType::from('college')` equals `UniversityType::College`. `test_try_from_invalid_returns_null()` — assert `UniversityType::tryFrom('invalid')` is null.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/UniversityTypeTest.php`
- [ ] Tests pass when run with `./vendor/bin/phpunit tests/Unit/UniversityTypeTest.php`
- [ ] Verifies exactly 4 enum cases exist
- [ ] Verifies each case has correct string value
- [ ] Verifies `from()` works for valid values
- [ ] Verifies `tryFrom()` returns null for invalid values

## File(s) to Create/Modify
- `tests/Unit/UniversityTypeTest.php` (create)
