# US-022: Add --chunk and --retries Options to ImportCommand

## Story
As a developer, I want to configure chunk size and retry count via command options so that I can tune import behavior for large datasets or unreliable connections.

## Prerequisites
- US-019 (ImportCommand exists)
- US-012 (DataClient retry logic exists)

## Stack
- Laravel Artisan Command options
- PHP 8.2+

## Implementation Checklist
- [ ] Open `src/Console/ImportCommand.php`
- [ ] Add options to signature:
  - `{--chunk=500 : Number of records to process per batch}`
  - `{--retries=3 : Number of HTTP retry attempts}`
  - `{--no-update : Skip updating existing records}`
- [ ] Pass `$retries` to `DataClient::fetch()` call
- [ ] Pass `$noUpdate` (inverted) to `UniversityImporter::import()` as the `$updateExisting` parameter
- [ ] Use `$chunk` for future batch processing (store as property for now)

## Implementation Prompt
> Modify `src/Console/ImportCommand.php`. Add three options to the signature: `{--chunk=500 : Records per batch}`, `{--retries=3 : HTTP retry attempts}`, `{--no-update : Skip updating existing records}`. In handle(), pass `(int) $this->option('retries')` to the DataClient's fetch method. Pass `! $this->option('no-update')` as the `$updateExisting` parameter to the importer's import method. Store chunk as a variable for future use. Display the options in the console output header.

## Acceptance Criteria
- [ ] `--chunk`, `--retries`, and `--no-update` options are available
- [ ] Default chunk is 500, default retries is 3
- [ ] `--retries` value is passed to DataClient
- [ ] `--no-update` flag prevents updating existing records
- [ ] Options are displayed in command output for visibility
- [ ] `php artisan ud:import DE --chunk=100 --retries=5 --no-update` works

## File(s) to Create/Modify
- `src/Console/ImportCommand.php` (modify)
