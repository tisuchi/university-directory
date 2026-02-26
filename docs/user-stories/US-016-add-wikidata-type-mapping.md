# US-016: Add Wikidata Type Mapping to UniversityType Enum

## Story
As a package developer, I want a method on UniversityType that maps Wikidata's free-form type strings to the controlled enum values so that imported data uses consistent types.

## Prerequisites
- US-004 (UniversityType enum exists)

## Stack
- PHP 8.2+ Enums
- Static method pattern

## Implementation Checklist
- [ ] Open `src/Enums/UniversityType.php`
- [ ] Add a static method `fromWikidata(string $wikidataType): self`
- [ ] Define a mapping array of Wikidata strings to enum cases:
  - 'university', 'public university', 'private university', 'research university', 'technical university', 'polytechnic' → `University`
  - 'college', 'community college', 'liberal arts college' → `College`
  - 'institute', 'institute of technology', 'research institute' → `Institute`
  - 'academy', 'military academy', 'art academy' → `Academy`
- [ ] Normalize input with `strtolower(trim())`
- [ ] Default to `University` if no match found

## Implementation Prompt
> Add a static method `fromWikidata(string $wikidataType): self` to `src/Enums/UniversityType.php`. Create a mapping array: lowercase Wikidata type strings → enum cases. Map variations like 'public university', 'research university', 'technical university' → University. Map 'college', 'community college' → College. Map 'institute', 'institute of technology' → Institute. Map 'academy', 'military academy' → Academy. Normalize input with `strtolower(trim($wikidataType))`. Return `self::University` as default if no match.

## Acceptance Criteria
- [ ] `fromWikidata()` static method exists on UniversityType
- [ ] `UniversityType::fromWikidata('research university')` returns `UniversityType::University`
- [ ] `UniversityType::fromWikidata('community college')` returns `UniversityType::College`
- [ ] `UniversityType::fromWikidata('institute of technology')` returns `UniversityType::Institute`
- [ ] `UniversityType::fromWikidata('military academy')` returns `UniversityType::Academy`
- [ ] Unknown strings default to `UniversityType::University`
- [ ] Input is case-insensitive and trimmed

## File(s) to Create/Modify
- `src/Enums/UniversityType.php` (modify)
