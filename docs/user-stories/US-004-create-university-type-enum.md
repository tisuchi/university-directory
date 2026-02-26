# US-004: Create UniversityType Enum

## Story
As a developer, I want a PHP backed enum for university types so that type values are constrained and type-safe throughout the package.

## Prerequisites
- US-002 (directory structure with `src/Enums/` exists)

## Stack
- PHP 8.2+ (native enums)
- No external dependencies

## Implementation Checklist
- [ ] Create `src/Enums/UniversityType.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Enums`
- [ ] Define as `enum UniversityType: string` (string-backed)
- [ ] Add case `University = 'university'`
- [ ] Add case `College = 'college'`
- [ ] Add case `Institute = 'institute'`
- [ ] Add case `Academy = 'academy'`

## Implementation Prompt
> Create a PHP 8.2 string-backed enum at `src/Enums/UniversityType.php` in namespace `Tisuchi\UniversityDirectory\Enums`. Define four cases: University ('university'), College ('college'), Institute ('institute'), Academy ('academy'). No additional methods needed — keep it minimal.

## Acceptance Criteria
- [ ] File exists at `src/Enums/UniversityType.php`
- [ ] Namespace is `Tisuchi\UniversityDirectory\Enums`
- [ ] Enum is string-backed (`enum UniversityType: string`)
- [ ] Has exactly 4 cases: University, College, Institute, Academy
- [ ] String values are lowercase: 'university', 'college', 'institute', 'academy'
- [ ] No extra methods or traits — just the enum cases
- [ ] File is valid PHP

## File(s) to Create/Modify
- `src/Enums/UniversityType.php` (create)
