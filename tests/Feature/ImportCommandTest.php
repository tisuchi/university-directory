<?php

use Illuminate\Support\Facades\Http;
use Tisuchi\UniversityDirectory\Models\University;

function sampleJsonResponse(int $count = 3): array
{
    $data = [];

    for ($i = 0; $i < $count; $i++) {
        $data[] = [
            'wikidata_id' => 'Q'.($i + 50000),
            'name' => "University of Testing {$i}",
            'short_name' => "UT{$i}",
            'official_website' => "https://ut{$i}.edu",
            'aliases' => ["UT{$i}"],
            'latitude' => 48.0 + $i,
            'longitude' => 11.0 + $i,
            'type' => 'university',
        ];
    }

    return $data;
}

// US-047: Basic import command tests
test('imports single country', function () {
    Http::fake([
        '*/DE.json' => Http::response(sampleJsonResponse(3), 200),
    ]);

    $this->artisan('ud:import', ['countries' => ['DE']])
        ->assertExitCode(0);

    expect(University::count())->toBe(3);
    expect(University::where('country_code', 'DE')->count())->toBe(3);
});

test('imports multiple countries', function () {
    Http::fake([
        '*/DE.json' => Http::response(sampleJsonResponse(2), 200),
        '*/US.json' => Http::response([
            [
                'wikidata_id' => 'Q100000',
                'name' => 'American University',
                'short_name' => 'AU',
                'official_website' => null,
                'aliases' => null,
                'latitude' => null,
                'longitude' => null,
                'type' => 'university',
            ],
        ], 200),
    ]);

    $this->artisan('ud:import', ['countries' => ['DE', 'US']])
        ->assertExitCode(0);

    expect(University::where('country_code', 'DE')->count())->toBe(2);
    expect(University::where('country_code', 'US')->count())->toBe(1);
});

test('handles fetch failure gracefully', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $this->artisan('ud:import', ['countries' => ['XX']])
        ->assertExitCode(0);

    expect(University::count())->toBe(0);
});

test('displays summary output', function () {
    Http::fake([
        '*/DE.json' => Http::response(sampleJsonResponse(2), 200),
    ]);

    $this->artisan('ud:import', ['countries' => ['DE']])
        ->expectsOutputToContain('created')
        ->assertExitCode(0);
});

// US-048: Import command options
test('no-update flag skips existing', function () {
    University::factory()->create([
        'wikidata_id' => 'Q50000',
        'name' => 'Old Name',
        'country_code' => 'DE',
    ]);

    Http::fake([
        '*/DE.json' => Http::response([
            [
                'wikidata_id' => 'Q50000',
                'name' => 'New Name',
                'short_name' => null,
                'official_website' => null,
                'aliases' => null,
                'latitude' => null,
                'longitude' => null,
                'type' => 'university',
            ],
        ], 200),
    ]);

    $this->artisan('ud:import', ['countries' => ['DE'], '--no-update' => true])
        ->assertExitCode(0);

    expect(University::where('wikidata_id', 'Q50000')->first()->name)->toBe('Old Name');
});

test('region flag imports region', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $this->artisan('ud:import', ['--region' => 'oceania'])
        ->assertExitCode(0);
});

test('all flag with no-interaction', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $this->artisan('ud:import', ['--all' => true, '--no-interaction' => true])
        ->assertExitCode(0);
});

test('fails without arguments', function () {
    $this->artisan('ud:import')
        ->assertExitCode(1);
});

test('fails with invalid region', function () {
    $this->artisan('ud:import', ['--region' => 'atlantis'])
        ->assertExitCode(1);
});
