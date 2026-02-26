# US-044: Test DataClient — Error Handling

## Story
As a package developer, I want tests that verify the DataClient throws proper exceptions on HTTP errors so that failures are caught and reported.

## Prerequisites
- US-011 (DataClient exists)
- US-012 (retry logic exists)
- US-043 (basic DataClient tests exist)

## Stack
- PHPUnit 11
- Laravel HTTP Client fake/mock
- PHP 8.2+

## Implementation Checklist
- [ ] Add tests to `tests/Unit/DataClientTest.php`
- [ ] Test: `test_throws_exception_on_404()` — Mock 404 response, assert exception is thrown when fetching
- [ ] Test: `test_throws_exception_on_500()` — Mock 500 response, assert exception is thrown
- [ ] Test: `test_throws_on_connection_timeout()` — Mock a connection exception, verify it propagates
- [ ] Test: `test_retries_before_failing()` — Mock sequence of failures then success, verify retry works

## Implementation Prompt
> Add tests to `tests/Unit/DataClientTest.php`. `test_throws_exception_on_404`: `Http::fake(['*' => Http::response('Not Found', 404)])`. Assert `$this->expectException(\Illuminate\Http\Client\RequestException::class)` then call `$client->fetch('XX')`. `test_throws_exception_on_500`: same but with 500 status. `test_retries_before_failing`: Use `Http::fakeSequence()->push('Error', 500)->push('Error', 500)->push([['name' => 'Test']], 200)`. Call `$client->fetch('DE', 3)` and assert it succeeds (retries got through). `test_throws_after_retries_exhausted`: fake all 500s, assert exception thrown.

## Acceptance Criteria
- [ ] 404 responses throw RequestException
- [ ] 500 responses throw RequestException
- [ ] Retry logic works (fails, retries, succeeds)
- [ ] After exhausting retries, exception is thrown
- [ ] No real HTTP calls are made in tests

## File(s) to Create/Modify
- `tests/Unit/DataClientTest.php` (modify)
