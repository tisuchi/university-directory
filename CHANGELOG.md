# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

## [0.2.1] - 2026-02-27

### Improved

- Added example output for each Artisan command in README (import, list, stats, sync)

## [0.2.0] - 2026-02-27

### Added

- `description` field for universities containing Wikipedia article intro summaries
- `university-directory:sync` command for maintainers to pull fresh data from remote source
- `--remote` flag on `university-directory:import` to fetch directly from remote instead of local files
- Bundled local dataset with 60,454 universities across 183 countries
- 7,142 universities enriched with Wikipedia descriptions
- `scripts/fetch-wikidata.php` and `scripts/fetch-wikidata-large.php` for populating data from Wikidata
- `scripts/fetch-wikipedia-descriptions.php` for enriching data with Wikipedia summaries

### Changed

- Import command now reads from local bundled JSON files by default (previously required remote fetch)

## [0.1.0] - 2026-02-26

### Added

- University model with `country`, `type`, and `search` query scopes
- `UniversityType` enum (`university`, `college`, `institute`, `academy`) with Wikidata mapping
- `university-directory:import` command to fetch university data by country, region, or all
- `university-directory:list` command with country, type, search, and limit filters
- `university-directory:stats` command showing database statistics
- `UniversityResource` API resource
- Automatic slug generation with collision handling
- Region mapping (Europe, Asia, Africa, Americas, Oceania, Middle East)
- Support for `--no-update`, `--chunk`, and `--retries` import options
- Laravel 11.x and 12.x support
- PHP 8.2, 8.3, and 8.4 support

[0.2.1]: https://github.com/tisuchi/university-directory/releases/tag/v0.2.1
[0.2.0]: https://github.com/tisuchi/university-directory/releases/tag/v0.2.0
[0.1.0]: https://github.com/tisuchi/university-directory/releases/tag/v0.1.0
