# US-017: Integrate Type Mapping in UniversityImporter

## Story
As a package developer, I want the importer to use the Wikidata type mapping when creating/updating universities so that type values are properly normalized during import.

## Prerequisites
- US-013 (UniversityImporter exists)
- US-016 (UniversityType::fromWikidata() exists)

## Stack
- PHP 8.2+
- UniversityType enum

## Implementation Checklist
- [ ] Open `src/Services/UniversityImporter.php`
- [ ] Import `Tisuchi\UniversityDirectory\Enums\UniversityType`
- [ ] In the `import()` loop, replace the hardcoded `'university'` type with:
  - `UniversityType::fromWikidata($item['type'] ?? 'university')->value`
- [ ] Handle case where `type` key is missing in source data (default to 'university')

## Implementation Prompt
> Modify the `import()` method in `src/Services/UniversityImporter.php`. Replace the hardcoded `'type' => 'university'` with `'type' => UniversityType::fromWikidata($item['type'] ?? 'university')->value`. Import the `UniversityType` enum at the top of the file. This ensures Wikidata's free-form type strings are mapped to the controlled enum values during import.

## Acceptance Criteria
- [ ] UniversityType enum is imported in the importer
- [ ] `type` field uses `UniversityType::fromWikidata()` for mapping
- [ ] Missing `type` key in source data defaults to 'university'
- [ ] Wikidata string "research university" maps to stored value "university"
- [ ] Wikidata string "community college" maps to stored value "college"
- [ ] No hardcoded type values remain in the import loop

## File(s) to Create/Modify
- `src/Services/UniversityImporter.php` (modify)
