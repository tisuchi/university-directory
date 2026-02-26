# US-010: Add Alias Search to scopeSearch

## Story
As a developer, I want the search scope to also search within the JSON aliases column so that searching "MIT" finds "Massachusetts Institute of Technology" even when "MIT" is stored as an alias.

## Prerequisites
- US-009 (scopeSearch exists with name/short_name search)

## Stack
- Laravel Eloquent Query Scopes
- JSON column querying (cross-database: MySQL JSON_CONTAINS / PostgreSQL jsonb / SQLite LIKE)

## Implementation Checklist
- [ ] Open `src/Models/University.php`
- [ ] Modify `scopeSearch` method
- [ ] Inside the grouped where closure, add `orWhere('aliases', 'LIKE', '%'.$term.'%')` for cross-database JSON alias search
- [ ] This simple LIKE approach works across MySQL, PostgreSQL, and SQLite without DB-specific JSON functions

## Implementation Prompt
> Modify the `scopeSearch` method in `src/Models/University.php`. Inside the existing grouped where closure, add a third `orWhere` clause: `$q->orWhere('aliases', 'LIKE', '%'.$term.'%')`. This searches the JSON aliases column using a simple LIKE which works across MySQL, PostgreSQL, and SQLite. The JSON column stores arrays like `["MIT", "Mass Tech"]`, so a LIKE search on the raw JSON string will match alias values.

## Acceptance Criteria
- [ ] `scopeSearch` now also searches the `aliases` JSON column
- [ ] Search uses `LIKE` on the `aliases` column (cross-database compatible)
- [ ] Searching "MIT" matches a record with `aliases: ["MIT", "Mass Tech"]`
- [ ] The orWhere is inside the grouped closure (no query leaks)
- [ ] Existing name and short_name search still works
- [ ] Works on MySQL, PostgreSQL, and SQLite (no DB-specific JSON functions)

## File(s) to Create/Modify
- `src/Models/University.php` (modify)
