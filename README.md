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

A lightweight, structured university registry for Laravel applications. Built for scholarship platforms, education portals, and any app that needs clean university data with search, autocomplete, and URL-friendly slugs.

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

Import university data from the curated data source by country code:

```bash
php artisan university-directory:import DE
php artisan university-directory:import DE US GB
php artisan university-directory:import --region=europe
php artisan university-directory:import --all
```

Available regions: `europe`, `asia`, `africa`, `americas`, `oceania`, `middle-east`

### Import Options

| Option | Default | Description |
|---|---|---|
| `--region` | — | Import all countries in a region |
| `--all` | — | Import all available countries |
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
    "official_website": "https://www.tum.de"
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

```bash
# List universities with filters
php artisan university-directory:list
php artisan university-directory:list --country=DE --type=university --search=Munich --limit=10

# Show database statistics
php artisan university-directory:stats
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
