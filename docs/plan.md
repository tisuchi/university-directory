# Laravel Education Registry - Package Plan

## 1. Package Vision

**Goal**

Provide a lightweight, structured, importable registry of educational institutions (universities, colleges, institutes) for Laravel applications.

**Non-Goals**

- Not a SaaS
- Not a global education authority
- Not a ranking system
- Not a city/country management system
- Not overly abstracted architecture

This is:

> A structured, maintainable, importable institution registry layer for Laravel.

---

## 2. Core Philosophy

- Minimal database schema
- No unnecessary abstractions
- No repository pattern
- No forced country relationships
- No heavy metadata
- No breaking the user's existing schema
- Clean Laravel-native code
- Import-driven, not seed-dump-driven

---

## 3. Data Source Strategy

### Primary Source: Wikidata (CC0)

Why:

- Free
- Legally safe (CC0)
- Structured
- Has unique identifiers (e.g. Q49108)
- Has aliases
- Has country relation
- Queryable via SPARQL

### AI Usage (Secondary)

AI is used for:

- Normalization
- Deduplication suggestions
- Slug improvement
- Alias enrichment
- Type classification cleanup

AI is NOT the primary dataset generator.

---

## 4. Database Design

All tables use a hardcoded `edu_` prefix to avoid conflicts with the host application.

> **Why hardcoded, not configurable?** Eloquent's `$table` property is static. A configurable prefix requires resolving config at boot time in the model constructor — fragile and error-prone. Migrations can't read config at generation time cleanly. Any JOIN or raw query from the user's app must dynamically resolve the prefix. No major Laravel package (Spatie, Filament, Cashier) uses configurable prefixes. If someone truly needs a different prefix, they can publish and edit migrations — that's the Laravel convention.

### Table: `edu_institutions`

| Column           | Type                     | Notes                                |
| ---------------- | ------------------------ | ------------------------------------ |
| id               | bigint                   | Primary key                          |
| wikidata_id      | string, nullable, unique | e.g. Q49108                          |
| name             | string                   | Official name                        |
| short_name       | string, nullable         | e.g. "MIT", "TUM" (from P1813)      |
| slug             | string, unique           | Indexed, generated via Str::slug()   |
| country_code     | string(2), indexed       | ISO 3166-1 alpha-2                   |
| type             | string                   | Backed by PHP enum                   |
| official_website | string, nullable         |                                      |
| latitude         | decimal(10,7), nullable  |                                      |
| longitude        | decimal(10,7), nullable  |                                      |
| created_at       | timestamp                |                                      |
| updated_at       | timestamp                |                                      |

**Indexes:**

- `UNIQUE(wikidata_id)` where not null
- `UNIQUE(slug)`
- `INDEX(country_code)`
- `INDEX(country_code, type)`

**Design decisions:**

- No foreign key to a countries table. Most apps already have countries — avoid FK conflicts and duplication.
- No `status` column in v1. Wikidata doesn't reliably indicate closures, and there's no mechanism to detect them. Add in v2 when a real data source exists. YAGNI.
- `short_name` added for real-world usage (autocomplete, dropdowns). Populated from Wikidata's short name property (P1813).

### Table: `edu_institution_aliases`

| Column         | Type      | Notes                            |
| -------------- | --------- | -------------------------------- |
| id             | bigint    | Primary key                      |
| institution_id | bigint    | Foreign key to edu_institutions  |
| alias          | string    | e.g. "MIT", "TU Munich"         |
| created_at     | timestamp |                                  |

Used for abbreviations, alternative spellings, and common short forms.

> **Why no `slug` column on aliases?** If the slug is just `Str::slug($alias)`, don't store it — derive it at query time or use a scope. Storing derived data in a lookup table adds write complexity for zero read benefit. Slug-based lookup should happen on the main `edu_institutions` table only.

### No `edu_import_logs` table in v1

Import logs add a table, a model, and write logic for something achievable with `$this->info()` in console output and `Log::info()` for persistence. Add in v2 if users actually need audit trails.

---

## 5. Package Configuration

`config/edu-registry.php`

```php
return [

    'import' => [
        'update_existing' => true,
        'chunk_size' => 500,
        'max_retries' => 3,
    ],

];
```

Minimal configuration. No configurable prefix (hardcoded `edu_`). No `default_status` (no status column in v1).

---

## 6. Institution Type Enum

Use a backed PHP enum instead of a free string to prevent data drift. Wikidata returns dozens of variants ("research university", "polytechnic", "technical university") — map them to a controlled set during import.

```php
enum InstitutionType: string
{
    case University = 'university';
    case College = 'college';
    case Institute = 'institute';
    case Academy = 'academy';
}
```

Anything from Wikidata that doesn't map cleanly defaults to `university`.

---

## 7. Core Features (Phase 1)

### Eloquent Models

**`Institution`** with scopes:

- `scopeCountry($code)`
- `scopeType($type)`
- `scopeSearch($term)` — basic LIKE search on name and short_name

**`InstitutionAlias`** — simple belongs-to relationship.

No repository pattern. No excessive abstraction.

### Facade

`EduRegistry::search('MIT')` as a clean public API for common lookups.

### Import Command

```
php artisan edu:import DE
php artisan edu:import DE --chunk=500
```

Workflow:

1. Call Wikidata SPARQL endpoint with pagination (`LIMIT 500 OFFSET x`)
2. Fetch institutions for the given ISO country code
3. Transform and normalize response
4. Match existing records by `wikidata_id` ONLY
5. Insert or update (upsert)
6. Output summary to console

### Import Matching Strategy

Match by `wikidata_id` only for automated imports.

> **Why not fallback to name + country_code?** Name matching creates duplicates. "Technical University of Munich" vs "Technische Universitat Munchen" — different strings, same institution. The `wikidata_id` is a unique identifier — use it. If `wikidata_id` is null (manual entry), it stays untouched by imports. Clean separation.

**Update rules — only update:**

- name
- short_name
- official_website
- latitude / longitude
- type

Never overwrite manually edited custom fields (if user extends model). Never auto-delete records missing from Wikidata.

### Slug Generation

Use `Str::slug()` directly in the importer. No dedicated `SlugGenerator` class.

> **Why no SlugGenerator?** Laravel has `Str::slug()`. Unless you're doing non-trivial transliteration of Cyrillic/Arabic university names, a dedicated class is unjustified. If transliteration is needed later, add it then. Don't pre-build infrastructure for a problem you haven't encountered yet.

---

## 8. SPARQL Reliability

The Wikidata public SPARQL endpoint has real constraints that must be handled:

- **60-second timeout** on queries
- **Aggressive rate limiting**
- **Large countries (USA: ~5,000+ institutions) will time out** without pagination
- **Endpoint downtime** is common

### Mitigations

1. **Pagination:** `LIMIT 500 OFFSET x` in every SPARQL query
2. **Retry logic:** Exponential backoff, 3 attempts per chunk
3. **Chunk option:** `--chunk=500` flag on the import command
4. **Response caching:** Cache raw SPARQL responses so re-runs don't re-fetch on failure

---

## 9. Package Structure

```
src/
  EduRegistryServiceProvider.php
  Facades/
    EduRegistry.php
  Models/
    Institution.php
    InstitutionAlias.php
  Enums/
    InstitutionType.php
  Services/
    WikidataClient.php
    InstitutionImporter.php
  Console/
    ImportInstitutionsCommand.php
config/
  edu-registry.php
database/
  migrations/
tests/
  Feature/
    ImportCommandTest.php
  Unit/
    WikidataClientTest.php
    InstitutionImporterTest.php
```

**Not included (by design):**

- No Repositories
- No Interfaces everywhere
- No Events / Jobs / Queues
- No DTO explosion
- No SlugGenerator class
- No import log model

---

## 10. Country Handling Strategy

Do NOT ship a countries table.

Store `country_code` (ISO 3166-1 alpha-2) as a plain string column with an index.

Reasons:

- Most apps already have countries
- Avoid FK conflicts
- Avoid duplication
- Keep the package portable

If advanced users want country model integration, they can map manually via config or model extension.

---

## 11. Rollout Plan

### Phase 1 — Germany Only

- Build the importer
- Test against real Wikidata responses
- Handle deduplication edge cases
- Validate slug generation
- Validate website fields
- Deploy inside your scholarship project for real-world validation (forms, filters, autocomplete)

### Phase 2 — Expand to 3-5 Countries

- Germany, USA, UK, Canada, Australia
- Validate SPARQL pagination works for large datasets (USA)
- Publish v0.1 on Packagist

### Phase 3 — Community Contributions

- Open up `php artisan edu:import FR` for any country
- Accept community PRs for type mapping improvements, alias enrichment
- Publish v1.0

---

## 12. Keeping It Lightweight

To ensure the package stays lightweight:

- No automatic global import
- No heavy seed files
- No bundled SQL dump
- No city-level data
- No ranking system
- No accreditation engine

This is a registry, not a ministry of education.

---

## 13. Long-Term Extension Possibilities (Post v1)

Not for v1. Document for future consideration:

- Fuzzy search helper
- Filament select component
- Livewire search dropdown
- Alias auto-suggest
- Scheduled sync command (`edu:sync`)
- Optional API routes
- `status` column (active/closed) with real data source
- `edu_import_logs` table for audit trails

---

## 14. What Makes This Package Valuable

Not the size of the data. But:

- Structured, clean data from a reliable source
- Automated importing with proper deduplication
- Minimal, conflict-free schema
- Laravel-native integration (Eloquent, Facade, Artisan)
- Alias handling for real-world name variations
- Slug consistency for URL-friendly lookups

---

## 15. Risks & Mitigation

| Risk                           | Mitigation                                             |
| ------------------------------ | ------------------------------------------------------ |
| Incomplete Wikidata entries    | Allow manual additions (null wikidata_id)              |
| Duplicates from name matching  | Match by wikidata_id only, never by name               |
| SPARQL timeouts on large data  | Pagination, chunking, retry with exponential backoff   |
| SPARQL endpoint downtime       | Response caching, retry logic                          |
| Type data drift (free strings) | PHP backed enum with controlled mapping                |
| Schema conflicts with host app | Hardcoded edu_ prefix, no FKs to external tables       |
| Overengineering                | Strict minimal architecture, no premature abstractions |
| Abandonment                    | Use it in your own production app first                |

---

## 16. Summary of Changes from Original Plan

| #  | Original Plan                    | Revised                                           |
| -- | -------------------------------- | ------------------------------------------------- |
| 1  | Configurable table prefix        | Hardcoded `edu_` prefix                           |
| 2  | `slug` on aliases table          | Dropped — derive from alias at query time         |
| 3  | `type` as free string            | PHP backed enum                                   |
| 4  | `status` column                  | Dropped from v1 (YAGNI)                           |
| 5  | Simple SPARQL call               | Chunking, retry, timeout handling, caching        |
| 6  | Name + country fallback matching | Match by `wikidata_id` only                       |
| 7  | No constraints mentioned         | Explicit unique/index constraints defined          |
| 8  | No short_name                    | Added `short_name` nullable column (from P1813)   |
| 9  | `edu_import_logs` table          | Dropped from v1 — use console output + Log facade |
| 10 | Minimal package structure        | Added Facades, Enums, tests, publishable config   |
| 11 | `SlugGenerator` class            | Removed — use `Str::slug()` directly              |
