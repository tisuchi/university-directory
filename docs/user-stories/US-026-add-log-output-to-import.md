# US-026: Add Log Output to ImportCommand

## Story
As a package developer, I want import operations to log their activity using Laravel's logger so that import results are persisted beyond the terminal session.

## Prerequisites
- US-019 (ImportCommand exists)

## Stack
- Laravel Logging (`Illuminate\Support\Facades\Log`)
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Console/ImportCommand.php`
- [ ] Import `Illuminate\Support\Facades\Log`
- [ ] Add `Log::info()` at the start of each country import: "Starting university import for {CC}"
- [ ] Add `Log::info()` after each country completes: "Imported {CC}: {created} created, {updated} updated, {skipped} skipped"
- [ ] Add `Log::error()` on fetch/import failure: "Failed to import {CC}: {error message}"
- [ ] Add `Log::info()` at the end with total summary

## Implementation Prompt
> Modify `src/Console/ImportCommand.php`. Import `Illuminate\Support\Facades\Log`. Add logging at key points: `Log::info("Starting university import for {$countryCode}")` before each fetch. `Log::info("Imported {$countryCode}: {$stats['created']} created, {$stats['updated']} updated, {$stats['skipped']} skipped")` after success. `Log::error("Failed to import {$countryCode}: {$e->getMessage()}")` in catch blocks. `Log::info("Import complete: {$totalCreated} created, {$totalUpdated} updated across {$count} countries")` at the end.

## Acceptance Criteria
- [ ] `Log` facade is imported
- [ ] Log entry at start of each country import
- [ ] Log entry on successful completion per country
- [ ] Log entry on failure per country (error level)
- [ ] Log entry with total summary at end
- [ ] Log messages include country code and stats
- [ ] Console output is not affected (logs are in addition to console output)

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (modify)
