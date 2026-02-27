<p align="center">
    <img src="art/logo.svg" width="128" alt="University Directory">
</p>

# University Directory

<p align="center">
    <a href="https://packagist.org/packages/tisuchi/university-directory"><img src="https://img.shields.io/packagist/v/tisuchi/university-directory.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://github.com/tisuchi/university-directory/actions"><img src="https://img.shields.io/github/actions/workflow/status/tisuchi/university-directory/run-tests.yml?branch=main&label=tests&style=flat-square" alt="Tests"></a>
    <a href="https://packagist.org/packages/tisuchi/university-directory"><img src="https://img.shields.io/packagist/dt/tisuchi/university-directory.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://github.com/tisuchi/university-directory/blob/main/LICENSE"><img src="https://img.shields.io/github/license/tisuchi/university-directory?style=flat-square" alt="License"></a>
</p>

A lightweight, structured university registry for Laravel applications. Ships with a bundled dataset of 60,000+ universities across 183 countries — no external API calls needed. Built for scholarship platforms, education portals, and any app that needs clean university data with search, autocomplete, and URL-friendly slugs.

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3.35+

## Installation

```bash
composer require tisuchi/university-directory
```

The package auto-discovers its service provider. Run the migration:

```bash
php artisan migrate
```

## Importing Data

The package ships with a bundled dataset of 60,000+ universities across 183 countries. Import by country code, region, or all at once:

```bash
php artisan university-directory:import DE
php artisan university-directory:import DE US GB
php artisan university-directory:import --region=europe
php artisan university-directory:import --all
```

By default the import reads from the local bundled files — no network access required. Use `--remote` to fetch fresh data from the remote source instead:

```bash
php artisan university-directory:import DE --remote
```

Available regions: `europe`, `asia`, `africa`, `americas`, `oceania`, `middle-east`

### Import Options

| Option | Default | Description |
|---|---|---|
| `--region` | — | Import all countries in a region |
| `--all` | — | Import all available countries |
| `--remote` | — | Fetch from remote source instead of local files |
| `--chunk` | 500 | Records per batch |
| `--retries` | 3 | HTTP retry attempts |
| `--no-update` | — | Skip updating existing records |

## Usage

### Querying Universities

```php
use Tisuchi\UniversityDirectory\Models\University;
use Tisuchi\UniversityDirectory\Enums\UniversityType;

// Filter by country
University::country('DE')->get();

// Filter by type
University::type(UniversityType::College)->get();

// Search by name, short name, or alias
University::search('Munich')->get();

// Combine scopes
University::country('US')->type(UniversityType::University)->search('MIT')->get();

// Find by slug
University::where('slug', 'technical-university-of-munich')->firstOrFail();

// Dropdown data
University::country('DE')->pluck('name', 'id');
```

### University Types

The `UniversityType` enum provides four cases:

```php
UniversityType::University  // 'university'
UniversityType::College     // 'college'
UniversityType::Institute   // 'institute'
UniversityType::Academy     // 'academy'
```

### Relationships

Add a relationship to your own models — no trait required:

```php
class Application extends Model
{
    public function university()
    {
        return $this->belongsTo(\Tisuchi\UniversityDirectory\Models\University::class);
    }
}
```

### API Resource

```php
use Tisuchi\UniversityDirectory\Http\Resources\UniversityResource;

// Single resource
return new UniversityResource($university);

// Collection
return UniversityResource::collection(
    University::search($request->q)->limit(20)->get()
);
```

Response format:

```json
{
    "id": 1,
    "name": "Technical University of Munich",
    "short_name": "TUM",
    "slug": "technical-university-of-munich",
    "country_code": "DE",
    "type": "university",
    "aliases": ["TU Munich", "TUM"],
    "official_website": "https://www.tum.de",
    "description": "The Technical University of Munich is a public research university in Munich, Germany."
}
```

### Autocomplete Endpoint

```php
Route::get('/universities/search', function (Request $request) {
    return University::search($request->q)
        ->country($request->country)
        ->limit(20)
        ->get(['id', 'name', 'short_name', 'country_code']);
});
```

## Artisan Commands

### Import Universities

```bash
php artisan university-directory:import DE US GB
```

```text
 3/3 [============================] 100%

+---------+---------+---------+---------+--------+
| Country | Created | Updated | Skipped | Status |
+---------+---------+---------+---------+--------+
| DE      | 428     | 0       | 0       | OK     |
| US      | 7,236   | 0       | 0       | OK     |
| GB      | 164     | 0       | 0       | OK     |
+---------+---------+---------+---------+--------+

Done. Total: 7,828 created, 0 updated, 0 skipped. Time: 4.2s
```

You can also import by region or everything at once:

```bash
php artisan university-directory:import --region=europe
php artisan university-directory:import --all
```

### List Universities

```bash
php artisan university-directory:list --country=DE --type=university --search=Munich --limit=5
```

```text
+----+--------------------------------------+------------+---------+------------+------------------------------------------+
| ID | Name                                 | Short Name | Country | Type       | Slug                                     |
+----+--------------------------------------+------------+---------+------------+------------------------------------------+
| 12 | Technical University of Munich        | TUM        | DE      | university | technical-university-of-munich            |
| 45 | Ludwig Maximilian University of Munich| LMU        | DE      | university | ludwig-maximilian-university-of-munich    |
| 78 | Munich University of Applied Sciences | MUAS       | DE      | university | munich-university-of-applied-sciences     |
+----+--------------------------------------+------------+---------+------------+------------------------------------------+

Showing 3 of 3 universities.
```

### Show Statistics

```bash
php artisan university-directory:stats
```

```text
University Directory Stats
--------------------------
Total universities: 60,312
Countries: 183
Types: university (45,210), college (8,430), institute (4,512), academy (2,160)
Top countries: US (7,236), IN (5,018), JP (3,412), GB (164), DE (428)
Last updated: 2026-02-27 10:15:00
```

### Sync Remote Data

For maintainers — fetch fresh data from the remote source into local bundled files:

```bash
php artisan university-directory:sync DE US --retries=5
php artisan university-directory:sync --all
```

```text
 2/2 [============================] 100%

+---------+--------------+--------+
| Country | Universities | Status |
+---------+--------------+--------+
| DE      | 428          | OK     |
| US      | 7,236        | OK     |
+---------+--------------+--------+

Sync complete. 2 succeeded, 0 failed.
```

## Publishing Migrations

To customize the migration:

```bash
php artisan vendor:publish --tag=university-directory-migrations
```

## Testing

```bash
composer test
```

## License

MIT
