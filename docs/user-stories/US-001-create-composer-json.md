# US-001: Create composer.json

## Story
As a package developer, I want a properly configured composer.json so that the package can be installed via Composer and auto-discovered by Laravel.

## Prerequisites
- None (this is the first story)

## Stack
- Composer / PHP 8.2+
- Laravel 11.x / 12.x

## Implementation Checklist
- [ ] Create `composer.json` at package root
- [ ] Set package name to `tisuchi/university-directory`
- [ ] Set description: "A lightweight, structured university registry for Laravel applications"
- [ ] Set type to `library`
- [ ] Set license to `MIT`
- [ ] Require `php: ^8.2` and `illuminate/support: ^11.0|^12.0`
- [ ] Require-dev: `orchestra/testbench: ^9.0|^10.0`, `pestphp/pest: ^3.0`
- [ ] Configure PSR-4 autoload: `Tisuchi\\UniversityDirectory\\` → `src/`
- [ ] Configure PSR-4 autoload-dev: `Tisuchi\\UniversityDirectory\\Tests\\` → `tests/`
- [ ] Add Laravel auto-discovery in `extra.laravel.providers`: `Tisuchi\\UniversityDirectory\\UniversityDirectoryServiceProvider`
- [ ] Add authors section with tisuchi's details
- [ ] Set minimum-stability to `dev` and prefer-stable to `true`

## Implementation Prompt
> Create the composer.json file for the `tisuchi/university-directory` Laravel package. PHP 8.2+, Laravel 11/12 support. Namespace: `Tisuchi\UniversityDirectory` mapped to `src/`. Include orchestra/testbench and pest as dev dependencies. Enable Laravel auto-discovery for the service provider.

## Acceptance Criteria
- [ ] `composer validate` passes without errors
- [ ] Package name is `tisuchi/university-directory`
- [ ] PHP requirement is `^8.2`
- [ ] Laravel support covers both 11.x and 12.x
- [ ] PSR-4 autoload maps `Tisuchi\\UniversityDirectory\\` to `src/`
- [ ] Laravel auto-discovery is configured for `UniversityDirectoryServiceProvider`
- [ ] Dev dependencies include orchestra/testbench and pest

## File(s) to Create/Modify
- `composer.json` (create)
