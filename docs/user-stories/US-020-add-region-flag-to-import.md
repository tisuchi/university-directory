# US-020: Add --region Flag to ImportCommand

## Story
As a developer, I want to import all universities for an entire region (e.g., `--region=europe`) so that I can quickly populate data for a geographic area.

## Prerequisites
- US-019 (ImportCommand exists)
- US-030 (Region mapping exists)

## Stack
- Laravel Artisan Command options
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Console/ImportCommand.php`
- [ ] Add option to signature: `{--region= : Import all countries in a region (europe, asia, africa, americas, oceania, middle-east)}`
- [ ] Import the region mapping (from US-030)
- [ ] In `handle()`, if `--region` is provided:
  - Look up the region in the mapping to get array of country codes
  - Merge with any explicit country arguments
  - If region is invalid, output error and return FAILURE
- [ ] Process all resolved country codes

## Implementation Prompt
> Modify `src/Console/ImportCommand.php`. Add `{--region= : Import all countries in a region}` to the signature. In handle(), check if `$this->option('region')` is set. If so, use the RegionMapping class (from Tisuchi\UniversityDirectory\Services\RegionMapping) to resolve the region name to an array of country codes. Merge these with any explicitly passed country arguments. If the region is invalid, output `$this->error("Unknown region: {$region}")` and return `self::FAILURE`. The countries argument should become optional: `{countries?* : ...}`.

## Acceptance Criteria
- [ ] `--region` option is available on the `ud:import` command
- [ ] `php artisan ud:import --region=europe` imports all European countries
- [ ] Region names supported: europe, asia, africa, americas, oceania, middle-east
- [ ] Invalid region names produce an error message
- [ ] `--region` can be combined with explicit country codes
- [ ] Countries argument becomes optional (either countries or --region must be provided)

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (modify)
