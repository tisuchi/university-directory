<?php

use Tisuchi\UniversityDirectory\Models\University;
use Tisuchi\UniversityDirectory\Services\UniversityImporter;

function sampleUniversities(int $count = 1): array
{
    $universities = [];

    for ($i = 0; $i < $count; $i++) {
        $universities[] = [
            'wikidata_id' => 'Q'.($i + 10000),
            'name' => "University {$i}",
            'short_name' => "U{$i}",
            'official_website' => "https://university{$i}.edu",
            'aliases' => ["Uni {$i}"],
            'latitude' => 48.0 + $i,
            'longitude' => 11.0 + $i,
            'type' => 'university',
        ];
    }

    return $universities;
}

// US-045: Insert tests
test('inserts new universities', function () {
    $importer = new UniversityImporter;
    $importer->import(sampleUniversities(3), 'DE');

    expect(University::count())->toBe(3);
});

test('sets correct attributes', function () {
    $importer = new UniversityImporter;
    $data = [
        [
            'wikidata_id' => 'Q49108',
            'name' => 'Technical University of Munich',
            'short_name' => 'TUM',
            'official_website' => 'https://www.tum.de',
            'aliases' => ['TU Munich', 'TUM'],
            'latitude' => 48.1497,
            'longitude' => 11.5679,
            'type' => 'university',
        ],
    ];

    $importer->import($data, 'DE');
    $uni = University::first();

    expect($uni->wikidata_id)->toBe('Q49108');
    expect($uni->name)->toBe('Technical University of Munich');
    expect($uni->short_name)->toBe('TUM');
    expect($uni->country_code)->toBe('DE');
    expect($uni->type->value)->toBe('university');
    expect($uni->official_website)->toBe('https://www.tum.de');
    expect($uni->aliases)->toBe(['TU Munich', 'TUM']);
});

test('returns correct created count', function () {
    $importer = new UniversityImporter;
    $stats = $importer->import(sampleUniversities(5), 'DE');

    expect($stats['created'])->toBe(5);
    expect($stats['updated'])->toBe(0);
    expect($stats['skipped'])->toBe(0);
});

test('generates slug from name', function () {
    $importer = new UniversityImporter;
    $importer->import([
        [
            'wikidata_id' => 'Q49108',
            'name' => 'Technical University of Munich',
            'short_name' => 'TUM',
            'official_website' => null,
            'aliases' => null,
            'latitude' => null,
            'longitude' => null,
            'type' => 'university',
        ],
    ], 'DE');

    expect(University::first()->slug)->toBe('technical-university-of-munich');
});

// US-046: Update tests
test('updates existing by wikidata id', function () {
    University::factory()->create([
        'wikidata_id' => 'Q49108',
        'name' => 'Old Name',
    ]);

    $importer = new UniversityImporter;
    $stats = $importer->import([
        [
            'wikidata_id' => 'Q49108',
            'name' => 'New Name',
            'short_name' => null,
            'official_website' => null,
            'aliases' => null,
            'latitude' => null,
            'longitude' => null,
            'type' => 'university',
        ],
    ], 'DE');

    expect(University::count())->toBe(1);
    expect(University::first()->name)->toBe('New Name');
    expect($stats['updated'])->toBe(1);
    expect($stats['created'])->toBe(0);
});

test('does not duplicate on reimport', function () {
    $data = sampleUniversities(3);

    $importer = new UniversityImporter;
    $importer->import($data, 'DE');
    $importer->import($data, 'DE');

    expect(University::count())->toBe(3);
});

test('skips update when no update flag', function () {
    University::factory()->create([
        'wikidata_id' => 'Q49108',
        'name' => 'Old Name',
    ]);

    $importer = new UniversityImporter;
    $stats = $importer->import([
        [
            'wikidata_id' => 'Q49108',
            'name' => 'New Name',
            'short_name' => null,
            'official_website' => null,
            'aliases' => null,
            'latitude' => null,
            'longitude' => null,
            'type' => 'university',
        ],
    ], 'DE', false);

    expect(University::first()->name)->toBe('Old Name');
    expect($stats['skipped'])->toBe(1);
    expect($stats['updated'])->toBe(0);
});

test('never matches null wikidata id', function () {
    University::factory()->create([
        'wikidata_id' => null,
        'name' => 'Manual University',
    ]);

    $importer = new UniversityImporter;
    $importer->import([
        [
            'wikidata_id' => null,
            'name' => 'Another Manual University',
            'short_name' => null,
            'official_website' => null,
            'aliases' => null,
            'latitude' => null,
            'longitude' => null,
            'type' => 'university',
        ],
    ], 'DE');

    expect(University::count())->toBe(2);
});
