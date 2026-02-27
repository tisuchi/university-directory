<?php

use Illuminate\Support\Facades\Http;
use Tisuchi\UniversityDirectory\Services\DataClient;

beforeEach(function () {
    $this->syncPath = sys_get_temp_dir().'/university-directory-test-sync-'.getmypid();

    if (! is_dir($this->syncPath)) {
        mkdir($this->syncPath, 0755, true);
    }

    $this->app->singleton(DataClient::class, function () {
        return new DataClient(dataPath: $this->syncPath);
    });
});

afterEach(function () {
    $files = glob($this->syncPath.'/*.json');
    foreach ($files as $file) {
        unlink($file);
    }
    if (is_dir($this->syncPath)) {
        rmdir($this->syncPath);
    }
});

test('syncs single country', function () {
    Http::fake([
        '*/DE.json' => Http::response([
            ['name' => 'TU Munich', 'wikidata_id' => 'Q49108'],
        ], 200),
    ]);

    $this->artisan('university-directory:sync', ['countries' => ['DE']])
        ->assertExitCode(0);

    $filePath = $this->syncPath.'/DE.json';
    expect(file_exists($filePath))->toBeTrue();

    $data = json_decode(file_get_contents($filePath), true);
    expect($data)->toBeArray()->toHaveCount(1);
    expect($data[0]['name'])->toBe('TU Munich');
});

test('syncs multiple countries', function () {
    Http::fake([
        '*/DE.json' => Http::response([['name' => 'TU Munich']], 200),
        '*/US.json' => Http::response([['name' => 'MIT']], 200),
    ]);

    $this->artisan('university-directory:sync', ['countries' => ['DE', 'US']])
        ->assertExitCode(0);

    expect(file_exists($this->syncPath.'/DE.json'))->toBeTrue();
    expect(file_exists($this->syncPath.'/US.json'))->toBeTrue();
});

test('handles sync failure gracefully', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $this->artisan('university-directory:sync', ['countries' => ['XX']])
        ->assertExitCode(1);
});

test('writes pretty-printed json', function () {
    Http::fake([
        '*/DE.json' => Http::response([['name' => 'TU Munich']], 200),
    ]);

    $this->artisan('university-directory:sync', ['countries' => ['DE']])
        ->assertExitCode(0);

    $contents = file_get_contents($this->syncPath.'/DE.json');
    expect($contents)->toContain("\n");
    expect(str_ends_with($contents, "\n"))->toBeTrue();
});

test('displays results table', function () {
    Http::fake([
        '*/DE.json' => Http::response([['name' => 'TU Munich']], 200),
    ]);

    $this->artisan('university-directory:sync', ['countries' => ['DE']])
        ->expectsOutputToContain('Sync complete')
        ->assertExitCode(0);
});

test('fails without arguments', function () {
    $this->artisan('university-directory:sync')
        ->assertExitCode(1);
});

test('fails with invalid region', function () {
    $this->artisan('university-directory:sync', ['--region' => 'atlantis'])
        ->assertExitCode(1);
});

test('supports region flag', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $this->artisan('university-directory:sync', ['--region' => 'oceania'])
        ->assertExitCode(0);
});
