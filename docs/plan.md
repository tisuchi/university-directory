# University Directory - Package Plan

## 1. Vision

A lightweight, structured, importable university registry for Laravel applications. Built for scholarship platforms, education portals, and any app that needs clean university data with search, autocomplete, and URL-friendly slugs.

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
- Developer experience over data plumbing — the consumption API is the product

---

## 3. Compatibility

- **PHP:** 8.2+
- **Laravel:** 11.x, 12.x
- **Database:** MySQL 8.0+, PostgreSQL 14+, SQLite 3.35+

---

## 4. Data Source Strategy

### Primary Source: Wikidata (CC0)

- Free and legally safe (CC0 license)
- Structured with unique identifiers (e.g. Q49108)
- Has aliases, country relations, coordinates
- Queryable via SPARQL

### Data Pipeline (Not In-Package)

SPARQL queries, data cleaning, and normalization happen outside the package in a separate data pipeline. The pipeline curates clean JSON files per country, hosted on GitHub. The package fetches from these curated files — not directly from the SPARQL endpoint.

This keeps the package truly lightweight. The data pipeline can be as sophisticated as needed (SPARQL, AI-assisted cleanup, manual curation) without burdening package consumers.

---

## 5. Database Design

All tables use a hardcoded `ud_` prefix to avoid conflicts with the host application.

> **Why hardcoded, not configurable?** Eloquent's `$table` property is static. A configurable prefix requires resolving config at boot time — fragile and error-prone. No major Laravel package (Spatie, Filament, Cashier) uses configurable prefixes. If someone needs a different prefix, they publish and edit migrations — that's the Laravel convention.

### Table: `ud_universities`

| Column           | Type                     | Notes                                         |
| ---------------- | ------------------------ | --------------------------------------------- |
| id               | bigint                   | Primary key                                   |
| wikidata_id      | string, nullable, unique | e.g. Q49108                                   |
| name             | string                   | Official name                                 |
| short_name       | string, nullable         | e.g. "MIT", "TUM" (from Wikidata P1813)       |
| slug             | string, unique           | Globally unique, country-suffixed if needed    |
| country_code     | string(2), indexed       | ISO 3166-1 alpha-2                            |
| type             | string                   | Backed by PHP enum                            |
| official_website | string, nullable         |                                               |
| aliases          | json, nullable           | e.g. ["MIT", "Mass Tech"]                     |
| latitude         | decimal(10,7), nullable  |                                               |
| longitude        | decimal(10,7), nullable  |                                               |
| created_at       | timestamp                |                                               |
| updated_at       | timestamp                |                                               |

**Indexes:**

- `UNIQUE(wikidata_id)` where not null
- `UNIQUE(slug)`
- `INDEX(country_code)`
- `INDEX(country_code, type)`

**Design decisions:**

- No foreign key to a countries table. Most apps already have countries — avoid FK conflicts and duplication.
- No `status` column in v1. Wikidata doesn't reliably indicate closures, and there's no mechanism to detect them. Add in v2 when a real data source exists.
- `short_name` for real-world usage (autocomplete, dropdowns). Populated from Wikidata's short name property (P1813).
- `aliases` stored as a JSON column instead of a separate table. Most universities have 1-3 aliases — a separate table with FK, model, relationship, and joins is too much machinery for v1. If alias needs grow (hundreds of aliases, alias metadata), add a dedicated table in v2.

### Slug Collision Strategy

Slugs are globally unique. When a collision is detected during import:

1. First attempt: `Str::slug($name)` → `national-university`
2. On collision: append country code → `national-university-ph`
3. Still colliding: append numeric suffix → `national-university-ph-2`

This keeps URL routing simple — no compound lookups needed.

### No `edu_import_logs` table in v1

Use console output and `Log::info()` for persistence. Add a dedicated table in v2 if users need audit trails.

---

## 6. University Type Enum

```php
enum UniversityType: string
{
    case University = 'university';
    case College = 'college';
    case Institute = 'institute';
    case Academy = 'academy';
}
```

Wikidata returns dozens of variants ("research university", "polytechnic", "technical university"). The importer maps them to this controlled set. Anything that doesn't map cleanly defaults to `university`.

---

## 7. Developer Experience (Consumption API)

The consumption API is the product. Import is plumbing. Here's how developers use the package:

### Basic Queries

```php
use Tisuchi\UniversityDirectory\Models\University;

// Get all German universities
University::country('DE')->get();

// Filter by type
University::country('US')->type(UniversityType::College)->get();

// Search by name or short_name
University::search('Munich')->get();

// Find by slug (for URL routing)
University::where('slug', 'technical-university-of-munich')->firstOrFail();

// Dropdown data
University::country('DE')->pluck('name', 'id');

// With alias search (searches name, short_name, and JSON aliases)
University::search('MIT')->first();
```

### Relationships

```php
// In the user's app — no trait needed, just a standard relationship
class Application extends Model
{
    public function university()
    {
        return $this->belongsTo(\Tisuchi\UniversityDirectory\Models\University::class);
    }
}

// Usage
$application->university->name; // "Massachusetts Institute of Technology"
$application->university->short_name; // "MIT"
$application->university->aliases; // ["MIT", "Mass Tech"]
```

### API Resources

```php
use Tisuchi\UniversityDirectory\Http\Resources\UniversityResource;

// In a controller
return UniversityResource::collection(
    University::search($request->q)->limit(20)->get()
);

// Response
{
    "id": 1,
    "name": "Technical University of Munich",
    "short_name": "TUM",
    "slug": "technical-university-of-munich",
    "country_code": "DE",
    "type": "university",
    "aliases": ["TU Munich", "TUM"],
    "official_website": "https://www.tum.de"
}
```

### Autocomplete Endpoint (Common Pattern)

```php
// routes/api.php
Route::get('/universities/search', function (Request $request) {
    return University::search($request->q)
        ->country($request->country)
        ->limit(20)
        ->get(['id', 'name', 'short_name', 'country_code']);
});
```

---

## 8. Artisan Commands

### Import

```
php artisan ud:import DE
php artisan ud:import DE --chunk=500 --retries=3 --no-update
```

Workflow:

1. Fetch curated JSON from the data repository for the given country code
2. Transform and normalize response
3. Match existing records by `wikidata_id` ONLY
4. Insert or update (upsert)
5. Handle slug collisions with country-code suffix
6. Output summary to console

**Import matching:** `wikidata_id` only. No name-based fallback — name matching creates duplicates across languages. Records with null `wikidata_id` (manual entries) are never touched by imports.

**Update rules — only update:** name, short_name, official_website, latitude, longitude, type, aliases. Never auto-delete records missing from the data source.

### List & Inspect

```
php artisan ud:list --country=DE
php artisan ud:list --search=Munich
php artisan ud:stats
```

Output for `ud:stats`:

```
University Directory Stats
--------------------------
Total universities: 1,247
Countries:          5
Types:              university (980), college (150), institute (87), academy (30)
Last import:        2026-02-20 (DE: 423 records)
```

These commands cost minimal effort to build and massively improve the developer experience of "did my import work?"

---

## 9. No Config File

Import-related settings (`chunk_size`, `retries`, `update_existing`) are per-invocation concerns — they belong as command options, not in a config file. If the config file would only have import keys, don't ship one. Not every package needs a config file.

If a config file becomes necessary in a future version (e.g., for customizing the model class or data source URL), add it then.

---

## 10. No Facade

The `University` model with Eloquent scopes IS the API. `University::search('MIT')->get()` is already clean and expressive. A Facade that wraps a single Eloquent query adds a layer of indirection for zero benefit. Facades exist for complex service objects, not for re-wrapping the query builder.

---

## 11. Package Structure

```
src/
  UniversityDirectoryServiceProvider.php
  Models/
    University.php
  Enums/
    UniversityType.php
  Services/
    DataClient.php
    UniversityImporter.php
  Http/
    Resources/
      UniversityResource.php
  Console/
    ImportCommand.php
    ListCommand.php
    StatsCommand.php
database/
  migrations/
tests/
  Feature/
    ImportCommandTest.php
    ListCommandTest.php
  Unit/
    DataClientTest.php
    UniversityImporterTest.php
    UniversityTest.php
    UniversityTypeTest.php
    SlugCollisionTest.php
```

**Not included (by design):**

- No Facades
- No Config file
- No Repositories
- No Interfaces everywhere
- No Events / Jobs / Queues
- No DTO explosion
- No separate aliases table/model

---

## 12. Testing Strategy

Tests are not optional. The Laravel ecosystem has high standards.

| Test File | What It Covers |
| --- | --- |
| `DataClientTest` | Mock HTTP responses, test JSON parsing, test error handling |
| `UniversityImporterTest` | Create, update, skip logic with SQLite. Upsert behavior. |
| `ImportCommandTest` | Full command with mocked HTTP. Console output assertions. |
| `ListCommandTest` | List/search output formatting. Stats calculations. |
| `UniversityTest` | `scopeCountry`, `scopeType`, `scopeSearch`. Alias JSON querying. |
| `UniversityTypeTest` | Wikidata type strings map to enum correctly. Default fallback. |
| `SlugCollisionTest` | Duplicate slug detection, country suffix, numeric suffix. |

All external HTTP calls are mocked. Tests run against SQLite for speed.

---

## 13. Country Handling

Do NOT ship a countries table.

Store `country_code` (ISO 3166-1 alpha-2) as a plain indexed string column.

- Most apps already have countries
- Avoid FK conflicts and duplication
- Keep the package portable

If users want country model integration, they add a standard `belongsTo` in their own app.

---

## 14. Rollout Plan

### Phase 1 — Germany Only

- Build the data pipeline (SPARQL → curated JSON)
- Build the package (model, importer, commands)
- Write full test suite
- Test against real Wikidata data
- Validate slug generation and collision handling
- Deploy inside your scholarship project for real-world validation (forms, filters, autocomplete)

### Phase 2 — Expand to 3-5 Countries

- Germany, USA, UK, Canada, Australia
- Validate import works for large datasets (USA)
- Publish v0.1 on Packagist

### Phase 3 — Community Contributions

- Open up `php artisan ud:import FR` for any country
- Accept community PRs for type mapping improvements, alias enrichment
- Publish v1.0

---

## 15. Keeping It Lightweight

- No automatic global import
- No heavy seed files
- No bundled SQL dump
- No city-level data
- No ranking system
- No accreditation engine

This is a registry, not a ministry of education.

---

## 16. Long-Term Possibilities (Post v1)

Not for v1. Document for future consideration:

- Fuzzy search helper
- Filament select component
- Livewire search dropdown
- Alias auto-suggest
- Scheduled sync command (`ud:sync`)
- Optional API routes (pre-built)
- `status` column (active/closed) with real data source
- `ud_import_logs` table for audit trails
- Separate aliases table if JSON column outgrows its usefulness
- Config file for model class customization, data source URL

---

## 17. Risks & Mitigation

| Risk | Mitigation |
| --- | --- |
| Incomplete Wikidata entries | Allow manual additions (null wikidata_id) |
| Duplicates from name matching | Match by wikidata_id only, never by name |
| Data source unavailable | Curated JSON on GitHub — no SPARQL dependency at runtime |
| Type data drift (free strings) | PHP backed enum with controlled mapping |
| Slug collisions across countries | Global unique slugs with country-code suffix |
| Schema conflicts with host app | Hardcoded ud_ prefix, no FKs to external tables |
| Overengineering | Strict minimal architecture, no premature abstractions |
| Abandonment | Use it in your own production app first |

---

## 18. Summary of Revisions

| # | Original (ChatGPT) | Architect Review | Final (Taylor Review) |
| -- | --- | --- | --- |
| 1 | Configurable table prefix | Hardcoded `edu_` | Hardcoded `ud_`, unified naming |
| 2 | `slug` on aliases table | Dropped slug column | Dropped entire aliases table — JSON column |
| 3 | `type` as free string | PHP backed enum | PHP backed enum |
| 4 | `status` column | Dropped from v1 | Dropped from v1 |
| 5 | Direct SPARQL in package | Chunking, retry, caching | Curated JSON data repo — no SPARQL in package |
| 6 | Name + country fallback | wikidata_id only | wikidata_id only |
| 7 | No constraints | Explicit indexes | Explicit indexes |
| 8 | No short_name | Added short_name | Added short_name |
| 9 | `edu_import_logs` table | Dropped from v1 | Dropped from v1 |
| 10 | Minimal structure | Added Facade, tests | Dropped Facade, expanded tests |
| 11 | `SlugGenerator` class | Use Str::slug() | Use Str::slug() with collision handling |
| 12 | Config file with 3 keys | Kept config file | Dropped config file — use command options |
| 13 | Model: `Institution` | Kept Institution | Renamed to `University` |
| 14 | Mixed package naming | — | Unified: `university-directory` everywhere |
| 15 | AI section in plan | — | Removed — it's workflow, not a feature |
| 16 | Import-heavy, no DX docs | — | Full consumption API section added |
| 17 | No artisan list/stats | — | Added `ud:list` and `ud:stats` commands |
| 18 | No version constraints | — | PHP 8.2+, Laravel 11/12, DB requirements |
| 19 | Slug collisions ignored | — | Global unique with country suffix strategy |
| 20 | 3 test files | — | 7 test files covering real scenarios |
