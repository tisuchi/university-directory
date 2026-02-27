<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tisuchi\UniversityDirectory\Services\DataClient;

// ── Local file reading (default behavior) ────────────────────────

test('fetches country data from local file', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');
    $result = $client->fetch('DE');

    expect($result)->toBeArray()->not->toBeEmpty();
    expect($result[0]['name'])->toBe('Technical University of Munich');
});

test('returns parsed json array from local file', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');
    $result = $client->fetch('DE');

    expect($result)->toBeArray()->toHaveCount(2);
});

test('fetchLocal reads correct file for country code', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');
    $result = $client->fetchLocal('US');

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0]['name'])->toBe('Massachusetts Institute of Technology');
});

test('country code is case insensitive for local files', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');
    $result = $client->fetch('de');

    expect($result)->toBeArray()->toHaveCount(2);
});

test('throws exception when local file does not exist', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');

    expect(fn () => $client->fetch('XX'))->toThrow(RuntimeException::class);
});

test('exception message suggests sync command when file missing', function () {
    $client = new DataClient(dataPath: __DIR__.'/../fixtures/data');

    try {
        $client->fetch('XX');
        test()->fail('Expected RuntimeException');
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toContain('university-directory:sync');
        expect($e->getMessage())->toContain('--remote');
    }
});

// ── Remote fetching (opt-in with $remote = true) ─────────────────

test('fetches country data from remote when flag is true', function () {
    Http::fake([
        '*/DE.json' => Http::response([
            ['name' => 'TU Munich', 'wikidata_id' => 'Q49108'],
        ], 200),
    ]);

    $client = new DataClient;
    $result = $client->fetch('DE', remote: true);

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0]['name'])->toBe('TU Munich');
});

test('constructs correct remote url', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $client = new DataClient(baseUrl: 'https://example.com/data');
    $client->fetch('US', remote: true);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://example.com/data/US.json');
    });
});

test('throws exception on remote 404', function () {
    Http::fake([
        '*' => Http::response('Not Found', 404),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX', remote: true))->toThrow(RequestException::class);
});

test('throws exception on remote 500', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX', remote: true))->toThrow(RequestException::class);
});

test('retries remote before failing then succeeds', function () {
    Http::fakeSequence()
        ->push('Error', 500)
        ->push('Error', 500)
        ->push([['name' => 'Test University']], 200);

    $client = new DataClient;
    $result = $client->fetch('DE', retries: 3, remote: true);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('throws after remote retries exhausted', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX', retries: 1, remote: true))
        ->toThrow(RequestException::class);
});
