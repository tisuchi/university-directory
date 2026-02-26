# US-034: Test Wikidata Type Mapping

## Story
As a package developer, I want tests that verify Wikidata type strings map correctly to UniversityType enum cases so that imported data gets the right types.

## Prerequisites
- US-016 (UniversityType::fromWikidata() exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- PHP 8.2+

## Implementation Checklist
- [ ] Open `tests/Unit/UniversityTypeTest.php` (or create if not yet existing)
- [ ] Add test: `test_maps_university_variants()` — 'public university', 'research university', 'technical university' all map to University
- [ ] Add test: `test_maps_college_variants()` — 'college', 'community college', 'liberal arts college' map to College
- [ ] Add test: `test_maps_institute_variants()` — 'institute', 'institute of technology' map to Institute
- [ ] Add test: `test_maps_academy_variants()` — 'academy', 'military academy' map to Academy
- [ ] Add test: `test_unknown_type_defaults_to_university()` — 'random string' maps to University
- [ ] Add test: `test_mapping_is_case_insensitive()` — 'PUBLIC UNIVERSITY' maps to University

## Implementation Prompt
> Add tests to `tests/Unit/UniversityTypeTest.php` for the `fromWikidata()` static method. Test `test_maps_university_variants`: assert `UniversityType::fromWikidata('research university')` equals `UniversityType::University`. Test multiple variants. `test_maps_college_variants`: 'community college' → College. `test_maps_institute_variants`: 'institute of technology' → Institute. `test_maps_academy_variants`: 'military academy' → Academy. `test_unknown_defaults_to_university`: 'xyz' → University. `test_mapping_is_case_insensitive`: 'PUBLIC UNIVERSITY' → University.

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
