# US-002: Create Package Directory Structure

## Story
As a package developer, I want the standard directory structure in place so that all subsequent stories have the correct file locations to work with.

## Prerequisites
- US-001 (composer.json exists)

## Stack
- Standard Laravel package directory conventions

## Implementation Checklist
- [ ] Create `src/` directory
- [ ] Create `src/Models/` directory
- [ ] Create `src/Enums/` directory
- [ ] Create `src/Services/` directory
- [ ] Create `src/Http/Resources/` directory
- [ ] Create `src/Console/` directory
- [ ] Create `database/migrations/` directory
- [ ] Create `tests/Feature/` directory
- [ ] Create `tests/Unit/` directory
- [ ] Add `.gitkeep` to each empty directory to ensure they are tracked by git

## Implementation Prompt
> Create the full directory structure for the university-directory Laravel package. Directories needed: src/ (with Models/, Enums/, Services/, Http/Resources/, Console/ subdirs), database/migrations/, tests/Feature/, tests/Unit/. Add .gitkeep files to empty directories.

## Acceptance Criteria
- [ ] All directories exist: `src/Models/`, `src/Enums/`, `src/Services/`, `src/Http/Resources/`, `src/Console/`
- [ ] `database/migrations/` directory exists
- [ ] `tests/Feature/` and `tests/Unit/` directories exist
- [ ] Empty directories contain `.gitkeep` files
- [ ] Directory structure matches the package plan layout

## File(s) to Create/Modify
- Multiple directories with `.gitkeep` files
