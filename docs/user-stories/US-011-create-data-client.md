# US-011: Create DataClient Service — Fetch JSON

## Story
As a package developer, I want a DataClient service that fetches curated university JSON data from a GitHub-hosted data repository so that the importer has a clean data source.

## Prerequisites
- US-002 (directory structure with `src/Services/` exists)
- US-003 (service provider exists)

## Stack
- Laravel HTTP Client (`Illuminate\Support\Facades\Http`)
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Services/DataClient.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Services`
- [ ] Add constructor that accepts an optional base URL (default: GitHub raw URL for the data repo)
- [ ] Add method `fetch(string $countryCode): array` that:
  - Constructs URL: `{baseUrl}/{countryCode}.json`
  - Makes HTTP GET request using Laravel's Http facade
  - Returns decoded JSON as array
  - Throws an exception if the response fails
- [ ] Use `Http::throw()->get()` for automatic exception on HTTP errors

## Implementation Prompt
> Create `src/Services/DataClient.php` in namespace `Tisuchi\UniversityDirectory\Services`. Constructor accepts an optional string `$baseUrl` with a sensible default (e.g., `https://raw.githubusercontent.com/tisuchi/university-data/main/data`). Add a `fetch(string $countryCode): array` method that uses Laravel's `Http::throw()->get("{$this->baseUrl}/{$countryCode}.json")` and returns `$response->json()`. Keep it simple — no caching, no retry logic yet.

## Acceptance Criteria
- [ ] File exists at `src/Services/DataClient.php`
- [ ] Namespace is `Tisuchi\UniversityDirectory\Services`
- [ ] Constructor accepts optional `$baseUrl` parameter with default
- [ ] `fetch()` method accepts a country code string
- [ ] `fetch()` returns an array (decoded JSON)
- [ ] Uses Laravel's HTTP client (`Http` facade)
- [ ] Throws on HTTP errors (4xx, 5xx)
- [ ] No caching or retry logic (those are separate stories)

## File(s) to Create/Modify
- `src/Services/DataClient.php` (create)
