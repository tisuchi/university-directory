<?php

use Tisuchi\UniversityDirectory\Models\University;
use Tisuchi\UniversityDirectory\Services\UniversityImporter;

function importSingle(string $name, string $countryCode, ?string $wikidataId = null): University
{
    $importer = new UniversityImporter;
    $importer->import([
        [
            'name' => $name,
            'wikidata_id' => $wikidataId ?? 'Q'.random_int(100000, 999999),
            'short_name' => null,
            'official_website' => null,
            'aliases' => null,
            'latitude' => null,
            'longitude' => null,
        ],
    ], $countryCode);

    return University::latest('id')->first();
}

// US-040: Basic slug generation
test('generates basic slug', function () {
    $uni = importSingle('Technical University of Munich', 'DE');

    expect($uni->slug)->toBe('technical-university-of-munich');
});

test('handles special characters in slug', function () {
    $uni = importSingle('Universitat Munchen', 'DE');

    expect($uni->slug)->toBe('universitat-munchen');
});

test('handles ampersand in slug', function () {
    $uni = importSingle('Arts & Sciences University', 'US');

    expect($uni->slug)->toBe('arts-sciences-university');
});

// US-041: Country code suffix on collision
test('appends country code on collision', function () {
    University::factory()->create(['slug' => 'national-university']);

    $uni = importSingle('National University', 'PH');

    expect($uni->slug)->toBe('national-university-ph');
});

test('country suffix is lowercase', function () {
    University::factory()->create(['slug' => 'national-university']);

    $uni = importSingle('National University', 'DE');

    expect($uni->slug)->toContain('-de');
    expect($uni->slug)->not->toContain('-DE');
});

// US-042: Numeric suffix on double collision
test('appends numeric suffix on double collision', function () {
    University::factory()->create(['slug' => 'national-university']);
    University::factory()->create(['slug' => 'national-university-ph']);

    $uni = importSingle('National University', 'PH');

    expect($uni->slug)->toBe('national-university-ph-2');
});

test('increments numeric suffix', function () {
    University::factory()->create(['slug' => 'national-university']);
    University::factory()->create(['slug' => 'national-university-ph']);
    University::factory()->create(['slug' => 'national-university-ph-2']);

    $uni = importSingle('National University', 'PH');

    expect($uni->slug)->toBe('national-university-ph-3');
});
