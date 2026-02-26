# US-030: Create Region-to-Country-Code Mapping

## Story
As a package developer, I want a hardcoded mapping of geographic regions to country codes so that the import command can support `--region=europe` and `--all` flags.

## Prerequisites
- US-002 (directory structure with `src/Services/` exists)

## Stack
- PHP 8.2+ (pure PHP class with static data)
- UN geoscheme-based region definitions

## Implementation Checklist
- [ ] Create `src/Services/RegionMapping.php`
- [ ] Use namespace `Tisuchi\UniversityDirectory\Services`
- [ ] Define a class with static methods
- [ ] Add constant or method `REGIONS` — associative array mapping region names to arrays of country codes:
  - `'europe'` → ['DE', 'FR', 'GB', 'NL', 'BE', 'CH', 'AT', 'SE', 'DK', 'NO', 'FI', 'IE', 'LU', 'IS', 'IT', 'ES', 'PT', 'PL', 'CZ', 'RO', 'HU', 'UA', 'GR', 'BG', 'HR', 'RS', 'SK', 'SI', 'LT', 'LV', 'EE', 'MT', 'CY', 'MC', 'LI', 'AD', 'SM', 'VA', 'MK', 'AL', 'BA', 'ME', 'MD', 'BY', 'RU']
  - `'asia'` → ['CN', 'JP', 'KR', 'IN', 'ID', 'MY', 'TH', 'PH', 'VN', 'SG', 'TW', 'HK', 'PK', 'BD', 'LK', 'NP', 'MM', 'KH', 'LA', 'MN', 'BN', 'MV', 'AF', 'BT', 'TL', 'KZ', 'UZ', 'GE', 'AM', 'AZ', 'TM', 'TJ', 'KG']
  - `'africa'` → ['NG', 'ZA', 'KE', 'GH', 'ET', 'TZ', 'UG', 'CM', 'SN', 'RW', 'MZ', 'ZW', 'ZM', 'CI', 'MG', 'AO', 'CD', 'ML', 'BF', 'NE', 'TD', 'GN', 'BJ', 'TG', 'SL', 'LR', 'MR', 'ER', 'DJ', 'SO', 'SS', 'NA', 'BW', 'LS', 'SZ', 'MW', 'MU', 'GA', 'CG', 'CF', 'GQ', 'CV', 'ST', 'KM', 'SC', 'GM', 'GW', 'BI']
  - `'americas'` → ['US', 'CA', 'BR', 'MX', 'AR', 'CO', 'CL', 'PE', 'VE', 'EC', 'BO', 'PY', 'UY', 'CU', 'DO', 'CR', 'PA', 'GT', 'HN', 'SV', 'NI', 'PR', 'JM', 'TT', 'HT', 'BB', 'BS', 'GY', 'SR', 'BZ', 'BM', 'KY']
  - `'oceania'` → ['AU', 'NZ', 'PG', 'FJ']
  - `'middle-east'` → ['EG', 'SA', 'AE', 'IR', 'IQ', 'IL', 'JO', 'LB', 'MA', 'TN', 'DZ', 'LY', 'QA', 'KW', 'BH', 'OM', 'PS', 'SY', 'YE', 'SD', 'TR']
- [ ] Add static method `get(string $region): ?array` — returns country codes for a region or null
- [ ] Add static method `all(): array` — returns all country codes (all regions merged, unique)
- [ ] Add static method `regions(): array` — returns list of valid region names

## Implementation Prompt
> Create `src/Services/RegionMapping.php` in namespace `Tisuchi\UniversityDirectory\Services`. Define a `REGIONS` constant as an associative array mapping region slugs (europe, asia, africa, americas, oceania, middle-east) to arrays of ISO 3166-1 alpha-2 country codes (based on the UN geoscheme). Add three static methods: `get(string $region): ?array` returns codes for a region or null if invalid. `all(): array` returns all unique country codes across all regions. `regions(): array` returns the list of valid region names (array keys). Include all countries from the plan's 10-phase rollout.

## Acceptance Criteria
- [ ] File exists at `src/Services/RegionMapping.php`
- [ ] Has 6 regions: europe, asia, africa, americas, oceania, middle-east
- [ ] `RegionMapping::get('europe')` returns array of European country codes
- [ ] `RegionMapping::get('invalid')` returns null
- [ ] `RegionMapping::all()` returns all unique country codes
- [ ] `RegionMapping::regions()` returns ['europe', 'asia', 'africa', 'americas', 'oceania', 'middle-east']
- [ ] Country codes match those listed in the plan's phase rollout
- [ ] All codes are uppercase ISO 3166-1 alpha-2

## File(s) to Create/Modify
- `src/Services/RegionMapping.php` (create)
