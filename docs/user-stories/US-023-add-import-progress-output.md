# US-023: Add Progress Bar and Summary Output to ImportCommand

## Story
As a developer, I want clear progress feedback and a final summary table when running imports so that I can monitor long-running imports and verify results.

## Prerequisites
- US-019 (ImportCommand exists with basic output)

## Stack
- Laravel Console Progress Bar
- Laravel Console Table output
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Console/ImportCommand.php`
- [ ] Add a progress bar when processing multiple countries: `$this->output->createProgressBar(count($countries))`
- [ ] Advance the progress bar after each country completes
- [ ] After all countries complete, output a summary table:
  - Columns: Country, Created, Updated, Skipped, Status
  - One row per country
- [ ] Output total counts at the bottom
- [ ] Output elapsed time

## Implementation Prompt
> Modify `src/Console/ImportCommand.php`. Add a progress bar for multi-country imports using `$this->withProgressBar()` or manual `$this->output->createProgressBar()`. After processing all countries, display a summary table using `$this->table(['Country', 'Created', 'Updated', 'Skipped', 'Status'], $rows)`. Each row shows the country code and its import stats. Add a final line: `$this->info("Done. Total: {$totalCreated} created, {$totalUpdated} updated, {$totalSkipped} skipped.")`. Include elapsed time calculation using microtime.

## Acceptance Criteria
- [ ] Progress bar shows during multi-country imports
- [ ] Summary table displays after all imports complete
- [ ] Each country has a row with created/updated/skipped counts
- [ ] Failed countries show "Failed" status in the table
- [ ] Total counts are displayed at the bottom
- [ ] Elapsed time is shown
- [ ] Single-country imports also show the summary (just one row)

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (modify)
