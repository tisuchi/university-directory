# US-021: Add --all Flag to ImportCommand

## Story
As a developer, I want an `--all` flag on the import command so that I can import universities for every available country in one command.

## Prerequisites
- US-019 (ImportCommand exists)
- US-030 (Region mapping exists — contains all country codes)

## Stack
- Laravel Artisan Command options
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Console/ImportCommand.php`
- [ ] Add option to signature: `{--all : Import all available countries}`
- [ ] In `handle()`, if `--all` is set:
  - Get all country codes from RegionMapping (all regions combined)
  - Use these as the country codes to process
- [ ] Add validation: at least one of countries, --region, or --all must be provided
- [ ] Add confirmation prompt: "This will import data for {count} countries. Continue?"

## Implementation Prompt
> Modify `src/Console/ImportCommand.php`. Add `{--all : Import all available countries}` to the signature. In handle(), if `$this->option('all')` is true, get all country codes from RegionMapping::all(). Add a confirmation: `$this->confirm("Import data for " . count($codes) . " countries?")`. Add validation at the start: if no countries, no --region, and no --all, output `$this->error('Provide country codes, --region, or --all')` and return FAILURE.

## Acceptance Criteria
- [ ] `--all` flag is available on `ud:import`
- [ ] `php artisan ud:import --all` attempts to import all countries
- [ ] Confirmation prompt appears before importing all countries
- [ ] Error message if no countries, --region, or --all is provided
- [ ] Works correctly with `--no-interaction` flag (skips confirmation)

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (modify)
