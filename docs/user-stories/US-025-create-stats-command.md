# US-025: Create StatsCommand

## Story
As a developer, I want an artisan command `ud:stats` that shows database statistics so that I can quickly verify the state of imported data.

## Prerequisites
- US-006 (University model exists)

## Stack
- Laravel Artisan Commands
- Eloquent aggregate queries
- PHP 8.2+

## Implementation Checklist
- [ ] Create `src/Console/StatsCommand.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Console`
- [ ] Set signature: `ud:stats`
- [ ] Set description: `Show university database statistics`
- [ ] In `handle()`:
  - Total university count: `University::count()`
  - Country count: `University::distinct('country_code')->count('country_code')`
  - Type breakdown: `University::groupBy('type')->selectRaw('type, count(*) as count')->pluck('count', 'type')`
  - Top 5 countries by count
  - Latest record's updated_at as "last updated"
- [ ] Format output as shown in the plan:
  ```
  University Directory Stats
  --------------------------
  Total universities: 1,247
  Countries:          5
  Types:              university (980), college (150), institute (87), academy (30)
  Last updated:       2026-02-20
  ```

## Implementation Prompt
> Create `src/Console/StatsCommand.php` in namespace `Tisuchi\UniversityDirectory\Console`. Signature: `ud:stats`. Description: "Show university database statistics". In handle(), calculate: total count, distinct country count, type breakdown (grouped), top 5 countries by count. Format output using `$this->line()` and `$this->info()`. Match the format from the plan: title, separator line, then aligned stats. Use `number_format()` for large numbers. Show type breakdown inline: "university (980), college (150)..." Show top 5 countries with counts.

## Acceptance Criteria
- [ ] File exists at `src/Console/StatsCommand.php`
- [ ] Command name is `ud:stats`
- [ ] Shows total university count
- [ ] Shows distinct country count
- [ ] Shows type breakdown with counts
- [ ] Shows top 5 countries by count
- [ ] Shows last updated date
- [ ] Numbers are formatted with commas (e.g., 1,247)
- [ ] Works gracefully with empty database (shows zeros)

## File(s) to Create/Modify
- `src/Console/StatsCommand.php` (create)
