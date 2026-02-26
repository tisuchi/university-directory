# US-019: Create ImportCommand — Single Country

## Story
As a developer, I want an artisan command `ud:import {country}` that imports university data for a single country so that I can populate the database from the curated data source.

## Prerequisites
- US-011 (DataClient exists)
- US-013 (UniversityImporter exists)
- US-003 (service provider exists)

## Stack
- Laravel Artisan Commands
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Console/ImportCommand.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Console`
- [ ] Extend `Illuminate\Console\Command`
- [ ] Set signature: `ud:import {countries* : One or more country codes (e.g. DE US UK)}`
- [ ] Set description: `Import universities from the curated data source`
- [ ] In `handle()`:
  - Resolve DataClient and UniversityImporter (via constructor injection or `app()`)
  - Get country codes from argument (array)
  - For the first version, process only the first country code
  - Call `$dataClient->fetch($countryCode)` to get data
  - Call `$importer->import($data, $countryCode)` to process
  - Output summary: "Imported {created} new, updated {updated}, skipped {skipped} universities for {CC}"
- [ ] Return `Command::SUCCESS`

## Implementation Prompt
> Create `src/Console/ImportCommand.php` in namespace `Tisuchi\UniversityDirectory\Console`. Extend Command. Signature: `ud:import {countries* : One or more country codes}`. In handle(), inject DataClient and UniversityImporter via constructor. Get the countries argument as array. Loop through each country code: fetch data via DataClient, import via UniversityImporter, output a summary line with created/updated/skipped counts. Use `$this->info()` and `$this->error()` for output. Wrap each country in try/catch to handle fetch failures gracefully. Return Command::SUCCESS.

## Acceptance Criteria
- [ ] File exists at `src/Console/ImportCommand.php`
- [ ] Command name is `ud:import`
- [ ] Accepts one or more country codes as arguments
- [ ] Fetches data using DataClient service
- [ ] Imports data using UniversityImporter service
- [ ] Outputs summary with created/updated/skipped counts
- [ ] Handles fetch errors gracefully (outputs error, continues to next country)
- [ ] Returns `Command::SUCCESS`

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (create)
