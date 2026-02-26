<?php

use Tisuchi\UniversityDirectory\Enums\UniversityType;
use Tisuchi\UniversityDirectory\Models\University;

// US-035: Model basics
test('uses correct table', function () {
    expect((new University)->getTable())->toBe('ud_universities');
});

test('has correct fillable', function () {
    $fillable = (new University)->getFillable();

    expect($fillable)->toContain('wikidata_id');
    expect($fillable)->toContain('name');
    expect($fillable)->toContain('short_name');
    expect($fillable)->toContain('slug');
    expect($fillable)->toContain('country_code');
    expect($fillable)->toContain('type');
    expect($fillable)->toContain('official_website');
    expect($fillable)->toContain('aliases');
    expect($fillable)->toContain('latitude');
    expect($fillable)->toContain('longitude');
});

test('type is cast to enum', function () {
    $uni = University::factory()->create();

    expect($uni->type)->toBeInstanceOf(UniversityType::class);
});

test('aliases is cast to array', function () {
    $uni = University::factory()->create(['aliases' => ['MIT', 'Mass Tech']]);

    expect($uni->fresh()->aliases)->toBeArray();
    expect($uni->fresh()->aliases)->toContain('MIT');
});

test('can be created with factory', function () {
    $uni = University::factory()->create();

    $this->assertDatabaseHas('ud_universities', ['id' => $uni->id]);
});

// US-036: scopeCountry
test('scope country filters by code', function () {
    University::factory()->count(3)->create(['country_code' => 'DE']);
    University::factory()->count(2)->create(['country_code' => 'US']);

    expect(University::country('DE')->count())->toBe(3);
    expect(University::country('US')->count())->toBe(2);
});

test('scope country is case insensitive', function () {
    University::factory()->count(3)->create(['country_code' => 'DE']);

    expect(University::country('de')->count())->toBe(3);
});

test('scope country returns empty for no matches', function () {
    University::factory()->count(2)->create(['country_code' => 'DE']);

    expect(University::country('XX')->count())->toBe(0);
});

// US-037: scopeType
test('scope type filters by enum', function () {
    University::factory()->count(2)->create(['type' => 'university']);
    University::factory()->count(3)->create(['type' => 'college']);

    expect(University::type(UniversityType::College)->count())->toBe(3);
});

test('scope type with university type', function () {
    University::factory()->count(2)->create(['type' => 'university']);
    University::factory()->count(3)->create(['type' => 'college']);

    expect(University::type(UniversityType::University)->count())->toBe(2);
});

test('scope type returns empty when no matches', function () {
    University::factory()->count(2)->create(['type' => 'university']);

    expect(University::type(UniversityType::Academy)->count())->toBe(0);
});

// US-038: scopeSearch name and short_name
test('search by name', function () {
    University::factory()->create(['name' => 'Technical University of Munich']);
    University::factory()->create(['name' => 'Harvard University']);

    expect(University::search('Munich')->count())->toBe(1);
});

test('search by short name', function () {
    University::factory()->create(['short_name' => 'TUM', 'name' => 'Technical University of Munich']);

    expect(University::search('TUM')->count())->toBe(1);
});

test('search partial match', function () {
    University::factory()->create(['name' => 'Technical University of Munich']);

    expect(University::search('Tech')->count())->toBe(1);
});

test('search returns empty for no match', function () {
    University::factory()->create(['name' => 'Technical University of Munich']);

    expect(University::search('XYZ123')->count())->toBe(0);
});

test('search with null returns all', function () {
    University::factory()->count(3)->create();

    expect(University::search(null)->count())->toBe(3);
});

test('search with empty string returns all', function () {
    University::factory()->count(3)->create();

    expect(University::search('')->count())->toBe(3);
});

// US-039: scopeSearch aliases
test('search by alias', function () {
    University::factory()->create([
        'name' => 'Massachusetts Institute of Technology',
        'aliases' => ['MIT', 'Mass Tech'],
    ]);

    expect(University::search('MIT')->count())->toBe(1);
});

test('search alias partial match', function () {
    University::factory()->create([
        'name' => 'Massachusetts Institute of Technology',
        'aliases' => ['MIT', 'Mass Tech'],
    ]);

    expect(University::search('Mass')->count())->toBe(1);
});

test('search combines name short name and alias', function () {
    University::factory()->create(['name' => 'MIT Academy', 'aliases' => null]);
    University::factory()->create(['name' => 'Some University', 'aliases' => ['MIT']]);

    expect(University::search('MIT')->count())->toBe(2);
});
