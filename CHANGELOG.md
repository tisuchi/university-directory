# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com), and this project adheres to [Semantic Versioning](https://semver.org).

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

[0.1.0]: https://github.com/tisuchi/university-directory/releases/tag/v0.1.0
