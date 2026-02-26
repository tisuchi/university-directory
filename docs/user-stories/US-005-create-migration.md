# US-005: Create ud_universities Migration

## Story
As a developer, I want the database migration for the universities table so that the package can store university data with proper schema and indexes.

## Prerequisites
- US-002 (directory structure with `database/migrations/` exists)

## Stack
- Laravel Migrations (Blueprint)
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3.35+ compatibility

## Implementation Checklist
- [ ] Create migration file at `database/migrations/create_ud_universities_table.php`
- [ ] Use a dateless filename (Laravel package convention — the service provider controls loading order)
- [ ] Table name: `ud_universities`
- [ ] Column: `id` — bigIncrements (primary key)
- [ ] Column: `wikidata_id` — string, nullable, unique
- [ ] Column: `name` — string
- [ ] Column: `short_name` — string, nullable
- [ ] Column: `slug` — string, unique
- [ ] Column: `country_code` — string(2), indexed
- [ ] Column: `type` — string (stores enum value)
- [ ] Column: `official_website` — string, nullable
- [ ] Column: `aliases` — json, nullable
- [ ] Column: `latitude` — decimal(10,7), nullable
- [ ] Column: `longitude` — decimal(10,7), nullable
- [ ] Column: `timestamps` (created_at, updated_at)
- [ ] Add composite index on `(country_code, type)`
- [ ] Add `down()` method that drops the table

## Implementation Prompt
> Create a Laravel migration at `database/migrations/create_ud_universities_table.php` (no date prefix). Table: `ud_universities`. Columns: id (bigIncrements), wikidata_id (string, nullable, unique), name (string), short_name (string, nullable), slug (string, unique), country_code (string 2 chars, indexed), type (string), official_website (string, nullable), aliases (json, nullable), latitude (decimal 10,7 nullable), longitude (decimal 10,7 nullable), timestamps. Add composite index on (country_code, type). Include down() to drop table.

## Acceptance Criteria
- [ ] Migration file exists at `database/migrations/create_ud_universities_table.php`
- [ ] Table name is `ud_universities` (hardcoded `ud_` prefix)
- [ ] All 12 columns are present with correct types
- [ ] `wikidata_id` has a unique index (nullable)
- [ ] `slug` has a unique index
- [ ] `country_code` has a single-column index
- [ ] Composite index exists on `(country_code, type)`
- [ ] `down()` method drops the `ud_universities` table
- [ ] Migration is cross-database compatible (MySQL, PostgreSQL, SQLite)
- [ ] No foreign keys to external tables

## File(s) to Create/Modify
- `database/migrations/create_ud_universities_table.php` (create)
