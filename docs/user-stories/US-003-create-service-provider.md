# US-003: Create Empty Service Provider Shell

## Story
As a package developer, I want a minimal service provider class so that Laravel can discover and boot the package.

## Prerequisites
- US-001 (composer.json with autoload configured)
- US-002 (directory structure exists)

## Stack
- PHP 8.2+
- Laravel ServiceProvider base class

## Implementation Checklist
- [ ] Create `src/UniversityDirectoryServiceProvider.php`
- [ ] Extend `Illuminate\Support\ServiceProvider`
- [ ] Use namespace `Tisuchi\UniversityDirectory`
- [ ] Add empty `register()` method
- [ ] Add empty `boot()` method with TODO comments for migrations and commands (to be filled in US-027, US-028)

## Implementation Prompt
> Create a minimal Laravel service provider at `src/UniversityDirectoryServiceProvider.php` for the `Tisuchi\UniversityDirectory` namespace. It should extend `Illuminate\Support\ServiceProvider` with empty `register()` and `boot()` methods. Add TODO comments in `boot()` indicating that migration loading and command registration will be added later.

## Acceptance Criteria
- [ ] File exists at `src/UniversityDirectoryServiceProvider.php`
- [ ] Namespace is `Tisuchi\UniversityDirectory`
- [ ] Class extends `Illuminate\Support\ServiceProvider`
- [ ] Has `register()` method (empty)
- [ ] Has `boot()` method (with TODO comments)
- [ ] File is valid PHP (no syntax errors)
- [ ] Class name matches the auto-discovery entry in composer.json

## File(s) to Create/Modify
- `src/UniversityDirectoryServiceProvider.php` (create)
