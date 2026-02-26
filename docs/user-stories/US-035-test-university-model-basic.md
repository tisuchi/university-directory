# US-035: Test University Model Basic Attributes

## Story
As a package developer, I want tests that verify the University model's table name, fillable attributes, and casts are configured correctly.

## Prerequisites
- US-006 (University model exists)
- US-032 (University factory exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Orchestra Testbench
- SQLite in-memory

## Implementation Checklist
- [ ] Create `tests/Unit/UniversityTest.php`
- [ ] Add test closure verifying `$table` is `'ud_universities'`
- [ ] Add test closure verifying all fillable fields are present
- [ ] Add test closure verifying `type` is cast to `UniversityType` enum
- [ ] Add test closure verifying `aliases` is cast to array
- [ ] Add test closure verifying creating a university via factory works
- [ ] Add test closure verifying `aliases` can store and retrieve array data

## Implementation Prompt
> Create `tests/Unit/UniversityTest.php`. Use Pest closure syntax (no class needed — base TestCase is bound via `tests/Pest.php`). Tests: `test('uses correct table', function () { expect((new University)->getTable())->toBe('ud_universities'); });` — `test('has correct fillable', function () { ... expect($fillable)->toContain('name'); ... });` — assert fillable contains all expected fields. `test('type is cast to enum', function () { $uni = University::factory()->create(); expect($uni->type)->toBeInstanceOf(UniversityType::class); });` — `test('aliases is cast to array', function () { $uni = University::factory()->create(['aliases' => ['MIT']]); expect($uni->aliases)->toBeArray(); });` — `test('can be created with factory', function () { University::factory()->create(); $this->assertDatabaseHas('ud_universities', []); });`

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
