# US-033: Test UniversityType Enum Values

## Story
As a package developer, I want tests that verify the UniversityType enum has the correct cases and values so that changes to the enum are caught immediately.

## Prerequisites
- US-004 (UniversityType enum exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- PHP 8.2+ Enums

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityTypeTest.php`
- [ ] Add test closure verifying enum has exactly 4 cases
- [ ] Add test closures verifying each case has the correct string value:
  - `University` → `'university'`
  - `College` → `'college'`
  - `Institute` → `'institute'`
  - `Academy` → `'academy'`
- [ ] Add test closure verifying `UniversityType::from('university')` returns `University` case
- [ ] Add test closure verifying `UniversityType::tryFrom('invalid')` returns null

## Implementation Prompt
> Create `tests/Unit/UniversityTypeTest.php`. Use Pest closure syntax (no class needed — base TestCase is bound via `tests/Pest.php`). Write tests: `test('has exactly four cases', function () { expect(UniversityType::cases())->toHaveCount(4); });` — `test('university case value', function () { expect(UniversityType::University->value)->toBe('university'); });` — similar for College, Institute, Academy. `test('can create from value', function () { expect(UniversityType::from('college'))->toBe(UniversityType::College); });` — `test('tryFrom invalid returns null', function () { expect(UniversityType::tryFrom('invalid'))->toBeNull(); });`

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/UniversityTypeTest.php`
- [ ] Tests pass when run with `./vendor/bin/pest tests/Unit/UniversityTypeTest.php`
- [ ] Verifies exactly 4 enum cases exist
- [ ] Verifies each case has correct string value
- [ ] Verifies `from()` works for valid values
- [ ] Verifies `tryFrom()` returns null for invalid values

## File(s) to Create/Modify
- `tests/Unit/UniversityTypeTest.php` (create)
