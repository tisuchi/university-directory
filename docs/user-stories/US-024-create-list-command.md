# US-024: Create ListCommand

## Story
As a developer, I want an artisan command `ud:list` that displays universities in a formatted table so that I can quickly inspect imported data from the terminal.

## Prerequisites
- US-006 (University model exists)
- US-007 (scopeCountry exists)
- US-009 (scopeSearch exists)

## Stack
- Laravel Artisan Commands
- Console table output
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Console/ListCommand.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Console`
- [ ] Set signature: `ud:list {--country= : Filter by country code} {--search= : Search by name} {--type= : Filter by type} {--limit=20 : Number of results}`
- [ ] Set description: `List universities in the database`
- [ ] In `handle()`:
  - Build query using University model with optional scopes
  - Apply `country()` scope if `--country` option is set
  - Apply `search()` scope if `--search` option is set
  - Apply `type()` scope if `--type` option is set
  - Limit results with `--limit`
  - Output results as a table with columns: ID, Name, Short Name, Country, Type, Slug
  - Show total count at bottom

## Implementation Prompt
> Create `src/Console/ListCommand.php` in namespace `Tisuchi\UniversityDirectory\Console`. Signature: `ud:list {--country= : Filter by country code} {--search= : Search by name} {--type= : Filter by type} {--limit=20 : Max results}`. In handle(), build a University query, conditionally apply country(), search(), type() scopes based on options. Limit with the --limit option. Get results and display using `$this->table(['ID', 'Name', 'Short Name', 'Country', 'Type', 'Slug'], $rows)`. Show `$this->info("Showing {$count} of {$total} universities")` at the end.

## Acceptance Criteria
- [ ] File exists at `src/Console/ListCommand.php`
- [ ] Command name is `ud:list`
- [ ] `--country`, `--search`, `--type`, `--limit` options work
- [ ] Output is a formatted table with: ID, Name, Short Name, Country, Type, Slug
- [ ] Default limit is 20 results
- [ ] Shows count summary at bottom
- [ ] No options returns first 20 universities
- [ ] Filters can be combined: `ud:list --country=DE --type=university`

## File(s) to Create/Modify
- `src/Console/ListCommand.php` (create)
