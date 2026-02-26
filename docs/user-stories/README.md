# University Directory — User Stories

> **50 granular user stories** for AI-driven implementation. Each story is standalone, takes ~2 minutes to implement, and includes prerequisites, implementation prompts, and acceptance criteria.

## How to Use

1. Pick a story that has all prerequisites completed
2. Give the **Implementation Prompt** to the AI
3. Verify against the **Acceptance Criteria**
4. Mark as done and move to the next story

## Implementation Order

Follow the numbered order. Stories within a group can be done in any order as long as prerequisites are met.

---

## Group 1: Package Foundation

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-001](US-001-create-composer-json.md) | Create composer.json | `composer.json` | Create |
| [US-002](US-002-create-directory-structure.md) | Create directory structure | Multiple dirs | Create |
| [US-003](US-003-create-service-provider.md) | Create empty service provider shell | `src/UniversityDirectoryServiceProvider.php` | Create |

---

## Group 2: Core — Enum & Migration

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-004](US-004-create-university-type-enum.md) | Create UniversityType enum | `src/Enums/UniversityType.php` | Create |
| [US-005](US-005-create-migration.md) | Create ud_universities migration | `database/migrations/create_ud_universities_table.php` | Create |

---

## Group 3: Model

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-006](US-006-create-university-model.md) | Create University model (basic) | `src/Models/University.php` | Create |
| [US-007](US-007-add-scope-country.md) | Add scopeCountry | `src/Models/University.php` | Modify |
| [US-008](US-008-add-scope-type.md) | Add scopeType | `src/Models/University.php` | Modify |
| [US-009](US-009-add-scope-search-basic.md) | Add scopeSearch (name + short_name) | `src/Models/University.php` | Modify |
| [US-010](US-010-add-scope-search-aliases.md) | Add alias search to scopeSearch | `src/Models/University.php` | Modify |

---

## Group 4: Services

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-011](US-011-create-data-client.md) | Create DataClient — fetch JSON | `src/Services/DataClient.php` | Create |
| [US-012](US-012-add-retry-to-data-client.md) | Add retry logic to DataClient | `src/Services/DataClient.php` | Modify |
| [US-013](US-013-create-university-importer.md) | Create UniversityImporter — basic insert | `src/Services/UniversityImporter.php` | Create |
| [US-014](US-014-add-upsert-logic.md) | Add upsert logic (match by wikidata_id) | `src/Services/UniversityImporter.php` | Modify |
| [US-015](US-015-add-slug-collision-handling.md) | Add slug collision handling | `src/Services/UniversityImporter.php` | Modify |
| [US-016](US-016-add-wikidata-type-mapping.md) | Add Wikidata type mapping to enum | `src/Enums/UniversityType.php` | Modify |
| [US-017](US-017-integrate-type-mapping-in-importer.md) | Integrate type mapping in importer | `src/Services/UniversityImporter.php` | Modify |

---

## Group 5: HTTP Resource

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-018](US-018-create-university-resource.md) | Create UniversityResource | `src/Http/Resources/UniversityResource.php` | Create |

---

## Group 6: Artisan Commands

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-019](US-019-create-import-command-basic.md) | Create ImportCommand — single country | `src/Console/ImportCommand.php` | Create |
| [US-020](US-020-add-region-flag-to-import.md) | Add --region flag to ImportCommand | `src/Console/ImportCommand.php` | Modify |
| [US-021](US-021-add-all-flag-to-import.md) | Add --all flag to ImportCommand | `src/Console/ImportCommand.php` | Modify |
| [US-022](US-022-add-chunk-retries-options.md) | Add --chunk, --retries, --no-update options | `src/Console/ImportCommand.php` | Modify |
| [US-023](US-023-add-import-progress-output.md) | Add progress bar and summary output | `src/Console/ImportCommand.php` | Modify |
| [US-024](US-024-create-list-command.md) | Create ListCommand | `src/Console/ListCommand.php` | Create |
| [US-025](US-025-create-stats-command.md) | Create StatsCommand | `src/Console/StatsCommand.php` | Create |
| [US-026](US-026-add-log-output-to-import.md) | Add log output to ImportCommand | `src/Console/ImportCommand.php` | Modify |

---

## Group 7: Service Provider Wiring

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-027](US-027-register-migrations.md) | Register migrations in service provider | `src/UniversityDirectoryServiceProvider.php` | Modify |
| [US-028](US-028-register-commands.md) | Register commands in service provider | `src/UniversityDirectoryServiceProvider.php` | Modify |
| [US-029](US-029-add-publishable-migrations.md) | Add publishable migrations | `src/UniversityDirectoryServiceProvider.php` | Modify |
| [US-030](US-030-create-region-mapping.md) | Create region-to-country-code mapping | `src/Services/RegionMapping.php` | Create |

---

## Group 8: Test Infrastructure

| # | Story | File | Creates/Modifies |
|---|-------|------|-----------------|
| [US-031](US-031-setup-test-infrastructure.md) | Set up Pest PHP + Orchestra Testbench | `phpunit.xml`, `tests/TestCase.php`, `tests/Pest.php` | Create |
| [US-032](US-032-create-university-factory.md) | Create University model factory | `database/factories/UniversityFactory.php`, `src/Models/University.php` | Create + Modify |

---

## Group 9: Unit Tests

| # | Story | Test File | Tests |
|---|-------|-----------|-------|
| [US-033](US-033-test-university-type-enum.md) | Test enum values | `tests/Unit/UniversityTypeTest.php` | 4 cases, values, from(), tryFrom() |
| [US-034](US-034-test-wikidata-type-mapping.md) | Test Wikidata type mapping | `tests/Unit/UniversityTypeTest.php` | fromWikidata(), defaults, case-insensitive |
| [US-035](US-035-test-university-model-basic.md) | Test model basics | `tests/Unit/UniversityTest.php` | table, fillable, casts, factory |
| [US-036](US-036-test-scope-country.md) | Test scopeCountry | `tests/Unit/UniversityTest.php` | filter, case-insensitive, empty |
| [US-037](US-037-test-scope-type.md) | Test scopeType | `tests/Unit/UniversityTest.php` | enum filter, empty results |
| [US-038](US-038-test-scope-search.md) | Test scopeSearch (name) | `tests/Unit/UniversityTest.php` | name, short_name, partial, null |
| [US-039](US-039-test-scope-search-aliases.md) | Test scopeSearch (aliases) | `tests/Unit/UniversityTest.php` | alias match, partial, combined |
| [US-040](US-040-test-slug-generation.md) | Test basic slug generation | `tests/Unit/SlugCollisionTest.php` | ASCII, special chars, ampersands |
| [US-041](US-041-test-slug-collision-country-suffix.md) | Test slug collision — country suffix | `tests/Unit/SlugCollisionTest.php` | collision → append country code |
| [US-042](US-042-test-slug-collision-numeric-suffix.md) | Test slug collision — numeric suffix | `tests/Unit/SlugCollisionTest.php` | double collision → numeric suffix |
| [US-043](US-043-test-data-client-success.md) | Test DataClient success | `tests/Unit/DataClientTest.php` | fetch, parse JSON, URL |
| [US-044](US-044-test-data-client-errors.md) | Test DataClient errors | `tests/Unit/DataClientTest.php` | 404, 500, retry, timeout |
| [US-045](US-045-test-importer-insert.md) | Test importer insert | `tests/Unit/UniversityImporterTest.php` | create, attributes, slug, counts |
| [US-046](US-046-test-importer-update.md) | Test importer update | `tests/Unit/UniversityImporterTest.php` | upsert, no-update, null wikidata_id |

---

## Group 10: Feature Tests

| # | Story | Test File | Tests |
|---|-------|-----------|-------|
| [US-047](US-047-test-import-command.md) | Test ImportCommand basic | `tests/Feature/ImportCommandTest.php` | single, multi, failure, output |
| [US-048](US-048-test-import-command-options.md) | Test ImportCommand options | `tests/Feature/ImportCommandTest.php` | --no-update, --region, --all |
| [US-049](US-049-test-list-command.md) | Test ListCommand | `tests/Feature/ListCommandTest.php` | list, filter, search, limit |
| [US-050](US-050-test-stats-command.md) | Test StatsCommand | `tests/Feature/StatsCommandTest.php` | counts, types, empty DB |

---

## Dependency Graph

```
US-001 → US-002 → US-003
                      ↓
              US-004  US-005
                ↓       ↓
              US-006 ←──┘
              ↓  ↓  ↓
         US-007 US-008 US-009 → US-010
              ↓
         US-011 → US-012
              ↓
         US-013 → US-014 → US-015
              ↓
         US-016 → US-017
              ↓
         US-018
              ↓
         US-019 → US-020 → US-021
              ↓       ↓
         US-022  US-023  US-026
              ↓
         US-024  US-025
              ↓
         US-027 → US-028 → US-029
              ↓
         US-030
              ↓
         US-031 → US-032
              ↓
         US-033 → US-034
         US-035 → US-036 → US-037 → US-038 → US-039
         US-040 → US-041 → US-042
         US-043 → US-044
         US-045 → US-046
              ↓
         US-047 → US-048
         US-049
         US-050
```

## Stats

- **Total stories:** 50
- **Create operations:** 19 new files
- **Modify operations:** 31 incremental changes
- **Source files:** 10 production files
- **Test files:** 7 test files
- **Estimated AI implementation time:** ~100 minutes (50 stories x ~2 min each)
