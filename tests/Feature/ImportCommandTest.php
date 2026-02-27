<?php

use Illuminate\Support\Facades\Http;
use Tisuchi\UniversityDirectory\Models\University;
use Tisuchi\UniversityDirectory\Services\DataClient;

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

beforeEach(function () {
    $this->app->singleton(DataClient::class, function () {
        return new DataClient(dataPath: __DIR__.'/../fixtures/data');
    });
});

// ── Local mode (default) ──────────────────────────────────────────

test('imports single country from local files', function () {
    $this->artisan('university-directory:import', ['countries' => ['DE']])
        ->assertExitCode(0);

    expect(University::count())->toBe(2);
    expect(University::where('country_code', 'DE')->count())->toBe(2);
});

test('imports multiple countries from local files', function () {
    $this->artisan('university-directory:import', ['countries' => ['DE', 'US']])
        ->assertExitCode(0);

    expect(University::where('country_code', 'DE')->count())->toBe(2);
    expect(University::where('country_code', 'US')->count())->toBe(1);
});

test('handles missing local file gracefully', function () {
    $this->artisan('university-directory:import', ['countries' => ['XX']])
        ->assertExitCode(0);

    expect(University::count())->toBe(0);
});

test('displays summary output', function () {
    $this->artisan('university-directory:import', ['countries' => ['DE']])
        ->expectsOutputToContain('created')
        ->assertExitCode(0);
});

// ── Remote mode (--remote flag) ──────────────────────────────────

test('imports from remote with --remote flag', function () {
    Http::fake([
        '*/DE.json' => Http::response(sampleJsonResponse(3), 200),
    ]);

    $this->artisan('university-directory:import', [
        'countries' => ['DE'],
        '--remote' => true,
    ])->assertExitCode(0);

    expect(University::count())->toBe(3);
});

test('handles remote fetch failure gracefully', function () {
    Http::fake([
        '*' => Http::response('Server Error', 500),
    ]);

    $this->artisan('university-directory:import', [
        'countries' => ['XX'],
        '--remote' => true,
    ])->assertExitCode(0);

    expect(University::count())->toBe(0);
});

// ── Options (work the same regardless of local/remote) ───────────

test('no-update flag skips existing', function () {
    University::factory()->create([
        'wikidata_id' => 'Q49108',
        'name' => 'Old Name',
        'country_code' => 'DE',
    ]);

    $this->artisan('university-directory:import', [
        'countries' => ['DE'],
        '--no-update' => true,
    ])->assertExitCode(0);

    expect(University::where('wikidata_id', 'Q49108')->first()->name)->toBe('Old Name');
});

test('region flag imports region', function () {
    $this->artisan('university-directory:import', ['--region' => 'oceania'])
        ->assertExitCode(0);
});

test('all flag with no-interaction', function () {
    $this->artisan('university-directory:import', ['--all' => true, '--no-interaction' => true])
        ->assertExitCode(0);
});

test('fails without arguments', function () {
    $this->artisan('university-directory:import')
        ->assertExitCode(1);
});

test('fails with invalid region', function () {
    $this->artisan('university-directory:import', ['--region' => 'atlantis'])
        ->assertExitCode(1);
});
