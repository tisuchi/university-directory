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

### Global Scale

The goal is full global coverage: **~195 countries, ~30,000+ institutions**. The rollout is phased to validate the pipeline early, stress-test with large datasets, and expand region by region until every country is covered.

Each phase adds curated JSON files to the data repository. The package itself doesn't change between phases — only the available data grows.

### New Commands for Global Coverage

```
php artisan ud:import DE                 # Single country
php artisan ud:import DE US UK           # Multiple countries
php artisan ud:import --region=europe    # Entire region
php artisan ud:import --all              # Everything
```

---

### Phase 1 — Foundation (1 country, ~400 institutions)

**Goal:** Validate the entire pipeline end-to-end.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Germany | DE | ~400 |

Deliverables:
- Build the data pipeline (SPARQL → curated JSON)
- Build the package (model, importer, commands)
- Write full test suite
- Validate slug generation and collision handling
- Deploy inside your scholarship project for real-world validation
- **Publish v0.1-alpha on Packagist**

---

### Phase 2 — Top Student Destinations (5 countries, ~10,000 institutions)

**Goal:** Stress-test with large datasets (USA, UK). Cover the countries that matter most for international scholarship platforms.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| United States | US | ~5,000 |
| United Kingdom | GB | ~160 |
| Canada | CA | ~380 |
| Australia | AU | ~170 |
| New Zealand | NZ | ~30 |

Deliverables:
- Validate pagination/chunking works for USA's ~5,000 institutions
- Validate slug collision handling at scale
- Test alias quality across English-speaking countries
- **Publish v0.1 on Packagist**

---

### Phase 3 — Western & Northern Europe (15 countries, ~4,500 institutions)

**Goal:** Strong Wikidata coverage region. Expand European footprint.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| France | FR | ~600 |
| Netherlands | NL | ~70 |
| Belgium | BE | ~50 |
| Switzerland | CH | ~40 |
| Austria | AT | ~70 |
| Sweden | SE | ~50 |
| Denmark | DK | ~40 |
| Norway | NO | ~40 |
| Finland | FI | ~40 |
| Ireland | IE | ~30 |
| Luxembourg | LU | ~5 |
| Iceland | IS | ~10 |
| Italy | IT | ~300 |
| Spain | ES | ~1,400 |
| Portugal | PT | ~130 |

Deliverables:
- Handle non-ASCII characters in slugs (umlauts, accents, Nordic letters)
- Validate type mapping for European institution types (Fachhochschule, Grande Ecole, Politecnico)
- **Publish v0.2**

---

### Phase 4 — East & Southeast Asia (12 countries, ~10,000 institutions)

**Goal:** Tackle the highest-volume countries (China, Japan, India, Indonesia). This phase alone may double the total dataset.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| China | CN | ~2,500 |
| Japan | JP | ~800 |
| South Korea | KR | ~400 |
| India | IN | ~5,000+ |
| Indonesia | ID | ~2,500 |
| Malaysia | MY | ~200 |
| Thailand | TH | ~300 |
| Philippines | PH | ~2,000 |
| Vietnam | VN | ~250 |
| Singapore | SG | ~30 |
| Taiwan | TW | ~160 |
| Hong Kong | HK | ~20 |

Deliverables:
- Handle CJK (Chinese/Japanese/Korean) character transliteration in slugs
- Handle Wikidata entries with names only in local script (no English label)
- Validate performance with 25,000+ total records in the database
- Add a `--language` flag to prefer English labels when available
- **Publish v0.3**

---

### Phase 5 — Eastern Europe, Balkans & Central Asia (20 countries, ~3,000 institutions)

**Goal:** Expand into Cyrillic-script countries and post-Soviet states.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Russia | RU | ~1,000 |
| Poland | PL | ~400 |
| Czech Republic | CZ | ~70 |
| Romania | RO | ~100 |
| Hungary | HU | ~65 |
| Ukraine | UA | ~300 |
| Turkey | TR | ~200 |
| Greece | GR | ~40 |
| Bulgaria | BG | ~50 |
| Croatia | HR | ~30 |
| Serbia | RS | ~50 |
| Slovakia | SK | ~35 |
| Slovenia | SI | ~10 |
| Lithuania | LT | ~25 |
| Latvia | LV | ~30 |
| Estonia | EE | ~20 |
| Kazakhstan | KZ | ~130 |
| Uzbekistan | UZ | ~80 |
| Georgia | GE | ~60 |
| Armenia | AM | ~30 |

Deliverables:
- Handle Cyrillic → Latin transliteration for slugs
- Validate Wikidata coverage for Central Asian countries (may be thin)
- Flag countries with low Wikidata coverage for manual supplementation
- **Publish v0.4**

---

### Phase 6 — Middle East & North Africa (20 countries, ~2,000 institutions)

**Goal:** Arabic-script region. Requires right-to-left name handling and Arabic transliteration.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Egypt | EG | ~60 |
| Saudi Arabia | SA | ~80 |
| UAE | AE | ~70 |
| Iran | IR | ~500 |
| Iraq | IQ | ~100 |
| Israel | IL | ~60 |
| Jordan | JO | ~30 |
| Lebanon | LB | ~40 |
| Morocco | MA | ~200 |
| Tunisia | TN | ~70 |
| Algeria | DZ | ~100 |
| Libya | LY | ~30 |
| Qatar | QA | ~15 |
| Kuwait | KW | ~15 |
| Bahrain | BH | ~15 |
| Oman | OM | ~30 |
| Palestine | PS | ~15 |
| Syria | SY | ~30 |
| Yemen | YE | ~30 |
| Sudan | SD | ~40 |

Deliverables:
- Handle Arabic → Latin transliteration for slugs
- Store English name as `name`, original script in aliases where available
- **Publish v0.5**

---

### Phase 7 — Latin America & Caribbean (30 countries, ~6,000 institutions)

**Goal:** Spanish/Portuguese-speaking Americas. Large volume from Brazil, Mexico, Argentina.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Brazil | BR | ~1,500 |
| Mexico | MX | ~1,300 |
| Argentina | AR | ~1,700 |
| Colombia | CO | ~400 |
| Chile | CL | ~150 |
| Peru | PE | ~150 |
| Venezuela | VE | ~100 |
| Ecuador | EC | ~70 |
| Bolivia | BO | ~55 |
| Paraguay | PY | ~55 |
| Uruguay | UY | ~30 |
| Cuba | CU | ~50 |
| Dominican Republic | DO | ~45 |
| Costa Rica | CR | ~60 |
| Panama | PA | ~40 |
| Guatemala | GT | ~15 |
| Honduras | HN | ~20 |
| El Salvador | SV | ~30 |
| Nicaragua | NI | ~60 |
| Puerto Rico | PR | ~30 |
| Jamaica | JM | ~10 |
| Trinidad & Tobago | TT | ~5 |
| Haiti | HT | ~15 |
| Barbados | BB | ~3 |
| Bahamas | BS | ~3 |
| Guyana | GY | ~3 |
| Suriname | SR | ~3 |
| Belize | BZ | ~3 |
| Bermuda | BM | ~2 |
| Cayman Islands | KY | ~2 |

Deliverables:
- Handle Spanish/Portuguese accented characters in slugs
- Validate Wikidata coverage for Caribbean micro-states (likely thin)
- **Publish v0.6**

---

### Phase 8 — Sub-Saharan Africa (48 countries, ~3,000 institutions)

**Goal:** Complete African continent coverage. Wikidata coverage will be thinnest here — plan for manual supplementation.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Nigeria | NG | ~250 |
| South Africa | ZA | ~100 |
| Kenya | KE | ~70 |
| Ghana | GH | ~50 |
| Ethiopia | ET | ~50 |
| Tanzania | TZ | ~50 |
| Uganda | UG | ~40 |
| Cameroon | CM | ~30 |
| Senegal | SN | ~20 |
| Rwanda | RW | ~30 |
| Mozambique | MZ | ~20 |
| Zimbabwe | ZW | ~20 |
| Zambia | ZM | ~30 |
| Cote d'Ivoire | CI | ~20 |
| Madagascar | MG | ~20 |
| Angola | AO | ~20 |
| DR Congo | CD | ~40 |
| Mali | ML | ~10 |
| Burkina Faso | BF | ~10 |
| Niger | NE | ~10 |
| Chad | TD | ~10 |
| Guinea | GN | ~10 |
| Benin | BJ | ~15 |
| Togo | TG | ~10 |
| Sierra Leone | SL | ~5 |
| Liberia | LR | ~5 |
| Mauritania | MR | ~10 |
| Eritrea | ER | ~5 |
| Djibouti | DJ | ~3 |
| Somalia | SO | ~10 |
| South Sudan | SS | ~5 |
| Namibia | NA | ~5 |
| Botswana | BW | ~5 |
| Lesotho | LS | ~3 |
| Eswatini | SZ | ~3 |
| Malawi | MW | ~10 |
| Mauritius | MU | ~10 |
| Gabon | GA | ~5 |
| Congo | CG | ~5 |
| Central African Rep. | CF | ~3 |
| Equatorial Guinea | GQ | ~2 |
| Cabo Verde | CV | ~5 |
| Sao Tome & Principe | ST | ~1 |
| Comoros | KM | ~2 |
| Seychelles | SC | ~2 |
| Gambia | GM | ~3 |
| Guinea-Bissau | GW | ~2 |
| Burundi | BI | ~10 |

Deliverables:
- Audit Wikidata coverage per country — flag gaps
- Publish a "coverage report" showing completeness per country
- Accept community contributions to fill gaps
- **Publish v0.7**

---

### Phase 9 — South Asia & Pacific Islands (15 countries, ~2,000 institutions)

**Goal:** Complete South Asian coverage and Pacific island nations.

| Country | Code | Est. Institutions |
| --- | --- | --- |
| Pakistan | PK | ~250 |
| Bangladesh | BD | ~1,200 |
| Sri Lanka | LK | ~50 |
| Nepal | NP | ~400 |
| Myanmar | MM | ~170 |
| Cambodia | KH | ~120 |
| Laos | LA | ~30 |
| Mongolia | MN | ~100 |
| Brunei | BN | ~5 |
| Maldives | MV | ~3 |
| Afghanistan | AF | ~40 |
| Papua New Guinea | PG | ~10 |
| Fiji | FJ | ~5 |
| Timor-Leste | TL | ~5 |
| Bhutan | BT | ~10 |

Deliverables:
- Handle Devanagari, Bengali, Burmese, Khmer script transliteration
- **Publish v0.8**

---

### Phase 10 — Remaining Territories & Micro-States (~20 entities)

**Goal:** 100% global coverage. Every sovereign state and significant territory.

| Country/Territory | Code | Est. Institutions |
| --- | --- | --- |
| Malta | MT | ~5 |
| Cyprus | CY | ~15 |
| Monaco | MC | ~2 |
| Liechtenstein | LI | ~2 |
| Andorra | AD | ~1 |
| San Marino | SM | ~1 |
| Vatican City | VA | ~1 |
| North Macedonia | MK | ~10 |
| Albania | AL | ~20 |
| Bosnia & Herzegovina | BA | ~15 |
| Montenegro | ME | ~10 |
| Moldova | MD | ~20 |
| Belarus | BY | ~50 |
| Azerbaijan | AZ | ~40 |
| Turkmenistan | TM | ~20 |
| Tajikistan | TJ | ~30 |
| Kyrgyzstan | KG | ~50 |
| North Korea | KP | ~5 |
| Macau | MO | ~10 |
| Greenland | GL | ~1 |

Deliverables:
- Final coverage audit across all ~195 countries
- Publish coverage dashboard (% completeness per country)
- **Publish v1.0 — full global coverage**

---

### Phase Summary

| Phase | Region | Countries | Est. Institutions | Cumulative Total | Version |
| --- | --- | --- | --- | --- | --- |
| 1 | Foundation (Germany) | 1 | ~400 | ~400 | v0.1-alpha |
| 2 | Top Student Destinations | 5 | ~5,700 | ~6,100 | v0.1 |
| 3 | Western & Northern Europe | 15 | ~2,900 | ~9,000 | v0.2 |
| 4 | East & Southeast Asia | 12 | ~14,000 | ~23,000 | v0.3 |
| 5 | Eastern Europe & Central Asia | 20 | ~2,800 | ~25,800 | v0.4 |
| 6 | Middle East & North Africa | 20 | ~1,600 | ~27,400 | v0.5 |
| 7 | Latin America & Caribbean | 30 | ~6,000 | ~33,400 | v0.6 |
| 8 | Sub-Saharan Africa | 48 | ~1,000 | ~34,400 | v0.7 |
| 9 | South Asia & Pacific | 15 | ~2,400 | ~36,800 | v0.8 |
| 10 | Remaining & Micro-States | ~20 | ~300 | **~37,100** | **v1.0** |

### Data Quality Strategy

Not all countries have equal Wikidata coverage. The plan accounts for this:

- **Tier A (Strong coverage):** US, UK, DE, FR, CA, AU, JP, KR, NL, SE — Wikidata alone is sufficient. Automated pipeline.
- **Tier B (Moderate coverage):** Most of Europe, major Asian/Latin American countries — Wikidata as base, manual review for gaps.
- **Tier C (Thin coverage):** Sub-Saharan Africa, Caribbean micro-states, Central Asia, Pacific Islands — Wikidata as starting point, supplement with WHED (World Higher Education Database) cross-referencing and community contributions.

Each country's JSON file in the data repo includes a `coverage_confidence` field:

```json
{
  "country_code": "NG",
  "coverage_confidence": "moderate",
  "institution_count": 247,
  "last_updated": "2026-03-15",
  "notes": "Missing ~50 private universities. Community contributions welcome."
}
```

### Batch Import Support

For users who want everything:

```
php artisan ud:import --all                    # Import all available countries
php artisan ud:import --region=europe          # All European countries
php artisan ud:import --region=asia            # All Asian countries
php artisan ud:import --region=africa          # All African countries
php artisan ud:import --region=americas        # All American countries
php artisan ud:import --region=oceania         # All Oceanian countries
php artisan ud:import --region=middle-east     # All Middle Eastern countries
```

Region definitions are hardcoded in the package (UN geoscheme-based). No config needed.

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
