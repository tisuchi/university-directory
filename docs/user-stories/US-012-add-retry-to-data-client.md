# US-012: Add Retry Logic to DataClient

## Story
As a package developer, I want the DataClient to support configurable retry logic so that transient network failures don't break imports.

## Prerequisites
- US-011 (DataClient exists with basic fetch)

## Stack
- Laravel HTTP Client retry feature
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Services/DataClient.php`
- [ ] Modify `fetch()` to accept an optional `int $retries = 3` parameter
- [ ] Use Laravel's `Http::retry($retries, 100)->throw()->get(...)` instead of plain `Http::throw()->get()`
- [ ] The 100ms is the delay between retries
- [ ] Log a warning on retry using `Log::warning()`

## Implementation Prompt
> Modify the `fetch()` method in `src/Services/DataClient.php` to accept an optional `int $retries = 3` parameter. Replace `Http::throw()->get(...)` with `Http::retry($retries, 100)->throw()->get(...)`. Import `Illuminate\Support\Facades\Log` and add `Log::warning("Fetching university data for {$countryCode}, attempt retry...")` inside a retry callback if Laravel's HTTP client supports it, or just let the retry happen silently.

## Acceptance Criteria
- [ ] `fetch()` method now accepts optional `$retries` parameter (default: 3)
- [ ] Uses `Http::retry()` for automatic retries
- [ ] Still throws on final failure after all retries exhausted
- [ ] Default behavior (no args) retries 3 times
- [ ] `fetch('DE', 1)` would only try once (no retries)

## File(s) to Create/Modify
- `src/Services/DataClient.php` (modify)
