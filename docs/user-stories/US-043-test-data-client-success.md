# US-043: Test DataClient — Successful Fetch

## Story
As a package developer, I want tests that verify the DataClient correctly fetches and parses JSON data so that the import pipeline has reliable data.

## Prerequisites
- US-011 (DataClient exists)
- US-031 (test infrastructure exists)

## Stack
- Pest PHP 3
- Laravel HTTP Client fake/mock
- PHP 8.2+

## Implementation Checklist
- [ ] Create `tests/Unit/DataClientTest.php`
- [ ] Test: `test('fetches country data successfully', function () { ... })` — Mock HTTP response with sample JSON, assert `fetch('DE')` returns expected array
- [ ] Test: `test('constructs correct url', function () { ... })` — Verify the URL is built correctly from base URL + country code
- [ ] Test: `test('returns array from json', function () { ... })` — Mock response with array of universities, assert result is a PHP array

## Implementation Prompt
> Create `tests/Unit/DataClientTest.php` using Pest closure syntax. The TestCase is bound via `tests/Pest.php` — no class or extends needed. Use `Http::fake()` to mock responses. `test('fetches country data successfully', function () { ... })`: Fake `Http::fake(['*/DE.json' => Http::response([['name' => 'TU Munich', 'wikidata_id' => 'Q49108']], 200)])`. Create DataClient, call `fetch('DE')`, use `expect($result)->toBeArray()->toHaveCount(1)` and `expect($result[0]['name'])->toBe('TU Munich')`. `test('returns parsed json array', function () { ... })`: similar but use `expect($result)->toBeArray()` to verify the result is a PHP array (not a string or object). `test('constructs correct url', function () { ... })`: use Http::fake with wildcard, call fetch('US'), assert Http::assertSent() was called with URL containing 'US.json'.

## Acceptance Criteria
- [ ] Test file exists at `tests/Unit/DataClientTest.php`
- [ ] HTTP calls are mocked (no real network requests)
- [ ] Verifies fetch returns parsed JSON as PHP array
- [ ] Verifies correct URL construction
- [ ] All tests pass

## File(s) to Create/Modify
- `tests/Unit/DataClientTest.php` (create)
