# US-035: Test University Model Basic Attributes

## Story
As a package developer, I want tests that verify the University model's table name, fillable attributes, and casts are configured correctly.

## Prerequisites
- US-006 (University model exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityTest.php`
- [ ] Extend base TestCase
- [ ] Test `$table` is `'ud_universities'`
- [ ] Test all fillable fields are present
- [ ] Test `type` is cast to `UniversityType` enum
- [ ] Test `aliases` is cast to array
- [ ] Test creating a university via factory works
- [ ] Test `aliases` can store and retrieve array data

## Implementation Prompt
> Create `tests/Unit/UniversityTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Unit`. Extend base TestCase. Tests: `test_uses_correct_table()` — `$this->assertEquals('ud_universities', (new University)->getTable())`. `test_has_correct_fillable()` — assert fillable contains all expected fields. `test_type_is_cast_to_enum()` — create a university with factory, assert `$uni->type` is instance of UniversityType. `test_aliases_is_cast_to_array()` — create uni with aliases `['MIT']`, assert `$uni->aliases` is array. `test_can_be_created_with_factory()` — `University::factory()->create()` and assert exists in DB.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/UniversityTest.php`
- [ ] Verifies table name is `ud_universities`
- [ ] Verifies all fillable fields
- [ ] Verifies type cast to UniversityType enum
- [ ] Verifies aliases cast to array
- [ ] Factory creates valid records in database
- [ ] All tests pass against SQLite in-memory

## File(s) to Create/Modify
- `tests/Unit/UniversityTest.php` (create)
