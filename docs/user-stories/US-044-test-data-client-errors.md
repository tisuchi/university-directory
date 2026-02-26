# US-044: Test DataClient — Error Handling

## Story
As a package developer, I want tests that verify the DataClient throws proper exceptions on HTTP errors so that failures are caught and reported.

## Prerequisites
- US-011 (DataClient exists)
- US-012 (retry logic exists)
- US-043 (basic DataClient tests exist)

## Stack
- Pest PHP 3
- Laravel HTTP Client fake/mock
- PHP 8.2+

## Implementation Checklist
- [ ] Add tests to `tests/Unit/DataClientTest.php`
- [ ] Test: `test('throws exception on 404', function () { ... })` — Mock 404 response, assert exception is thrown when fetching
- [ ] Test: `test('throws exception on 500', function () { ... })` — Mock 500 response, assert exception is thrown
- [ ] Test: `test('throws on connection timeout', function () { ... })` — Mock a connection exception, verify it propagates
- [ ] Test: `test('retries before failing', function () { ... })` — Mock sequence of failures then success, verify retry works

## Implementation Prompt
> Add tests to `tests/Unit/DataClientTest.php` using Pest closure syntax. `test('throws exception on 404', function () { ... })`: `Http::fake(['*' => Http::response('Not Found', 404)])`. Use `expect(fn () => $client->fetch('XX'))->toThrow(\Illuminate\Http\Client\RequestException::class)`. `test('throws exception on 500', function () { ... })`: same but with 500 status, use `expect(fn () => $client->fetch('XX'))->toThrow(RequestException::class)`. `test('retries before failing', function () { ... })`: Use `Http::fakeSequence()->push('Error', 500)->push('Error', 500)->push([['name' => 'Test']], 200)`. Call `$client->fetch('DE', 3)` and use `expect($result)->toBeArray()` to assert it succeeds (retries got through). `test('throws after retries exhausted', function () { ... })`: fake all 500s, use `expect(fn () => $client->fetch('XX'))->toThrow(RequestException::class)`.

## Acceptance Criteria
- [ ] 404 responses throw RequestException
- [ ] 500 responses throw RequestException
- [ ] Retry logic works (fails, retries, succeeds)
- [ ] After exhausting retries, exception is thrown
- [ ] No real HTTP calls are made in tests

## File(s) to Create/Modify
- `tests/Unit/DataClientTest.php` (modify)
