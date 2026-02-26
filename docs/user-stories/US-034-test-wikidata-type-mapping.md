# US-034: Test Wikidata Type Mapping

## Story
As a package developer, I want tests that verify Wikidata type strings map correctly to UniversityType enum cases so that imported data gets the right types.

## Prerequisites
- US-016 (UniversityType::fromWikidata() exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- PHP 8.2+

## Implementation Checklist
- [ ] Open `tests/Unit/UniversityTypeTest.php` (or create if not yet existing)
- [ ] Add test closure: `test('maps university variants', ...)` — 'public university', 'research university', 'technical university' all map to University
- [ ] Add test closure: `test('maps college variants', ...)` — 'college', 'community college', 'liberal arts college' map to College
- [ ] Add test closure: `test('maps institute variants', ...)` — 'institute', 'institute of technology' map to Institute
- [ ] Add test closure: `test('maps academy variants', ...)` — 'academy', 'military academy' map to Academy
- [ ] Add test closure: `test('unknown type defaults to university', ...)` — 'random string' maps to University
- [ ] Add test closure: `test('mapping is case insensitive', ...)` — 'PUBLIC UNIVERSITY' maps to University

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTypeTest.php` for the `fromWikidata()` static method using Pest closure syntax. `test('maps university variants', function () { expect(UniversityType::fromWikidata('research university'))->toBe(UniversityType::University); });` — test multiple variants. `test('maps college variants', ...)`: 'community college' → College. `test('maps institute variants', ...)`: 'institute of technology' → Institute. `test('maps academy variants', ...)`: 'military academy' → Academy. `test('unknown defaults to university', function () { expect(UniversityType::fromWikidata('xyz'))->toBe(UniversityType::University); });` — `test('mapping is case insensitive', function () { expect(UniversityType::fromWikidata('PUBLIC UNIVERSITY'))->toBe(UniversityType::University); });`

## Acceptance Criteria
- [ ] All Wikidata type mapping tests pass
- [ ] University variants (public, research, technical) map correctly
- [ ] College variants map correctly
- [ ] Institute variants map correctly
- [ ] Academy variants map correctly
- [ ] Unknown types default to University
- [ ] Mapping is case-insensitive

## File(s) to Create/Modify
- `tests/Unit/UniversityTypeTest.php` (modify)
