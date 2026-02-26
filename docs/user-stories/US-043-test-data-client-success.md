# US-043: Test DataClient — Successful Fetch

## Story
As a package developer, I want tests that verify the DataClient correctly fetches and parses JSON data so that the import pipeline has reliable data.

## Prerequisites
- US-011 (DataClient exists)
- US-031 (test infrastructure exists)

## Stack
- PHPUnit 11
- Laravel HTTP Client fake/mock
- PHP 8.2+

## Implementation Checklist
- [ ] Create `tests/Unit/DataClientTest.php`
- [ ] Extend base TestCase
- [ ] Test: `test_fetches_country_data_successfully()` — Mock HTTP response with sample JSON, assert `fetch('DE')` returns expected array
- [ ] Test: `test_constructs_correct_url()` — Verify the URL is built correctly from base URL + country code
- [ ] Test: `test_returns_array_from_json()` — Mock response with array of universities, assert result is a PHP array

## Implementation Prompt
> Create `tests/Unit/DataClientTest.php` in namespace `Tisuchi\UniversityDirectory\Tests\Unit`. Extend base TestCase. Use `Http::fake()` to mock responses. `test_fetches_country_data_successfully`: Fake `Http::fake(['*/DE.json' => Http::response([['name' => 'TU Munich', 'wikidata_id' => 'Q49108']], 200)])`. Create DataClient, call `fetch('DE')`, assert result is an array with one item and name is 'TU Munich'. `test_returns_parsed_json_array`: similar but verify the result is a PHP array (not a string or object). `test_constructs_correct_url`: use Http::fake with wildcard, call fetch('US'), assert Http::assertSent() was called with URL containing 'US.json'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/DataClientTest.php`
- [ ] HTTP calls are mocked (no real network requests)
- [ ] Verifies fetch returns parsed JSON as PHP array
- [ ] Verifies correct URL construction
- [ ] All tests pass

## File(s) to Create/Modify
- `tests/Unit/DataClientTest.php` (create)
