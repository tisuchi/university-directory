<?php

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tisuchi\UniversityDirectory\Services\DataClient;

// US-043: Successful fetch
test('fetches country data successfully', function () {
    Http::fake([
        '*/DE.json' => Http::response([
            ['name' => 'TU Munich', 'wikidata_id' => 'Q49108'],
        ], 200),
    ]);

    $client = new DataClient;
    $result = $client->fetch('DE');

    expect($result)->toBeArray()->toHaveCount(1);
    expect($result[0]['name'])->toBe('TU Munich');
});

test('returns parsed json array', function () {
    Http::fake([
        '*/US.json' => Http::response([
            ['name' => 'MIT'],
            ['name' => 'Harvard'],
        ], 200),
    ]);

    $client = new DataClient;
    $result = $client->fetch('US');

    expect($result)->toBeArray()->toHaveCount(2);
});

test('constructs correct url', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $client = new DataClient('https://example.com/data');
    $client->fetch('US');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'https://example.com/data/US.json');
    });
});

// US-044: Error handling
test('throws exception on 404', function () {
    Http::fake([
        '*' => Http::response('Not Found', 404),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX'))->toThrow(RequestException::class);
});

test('throws exception on 500', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX'))->toThrow(RequestException::class);
});

test('retries before failing then succeeds', function () {
    Http::fakeSequence()
        ->push('Error', 500)
        ->push('Error', 500)
        ->push([['name' => 'Test University']], 200);

    $client = new DataClient;
    $result = $client->fetch('DE', 3);

    expect($result)->toBeArray()->toHaveCount(1);
});

test('throws after retries exhausted', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $client = new DataClient;

    expect(fn () => $client->fetch('XX', 1))->toThrow(RequestException::class);
});
