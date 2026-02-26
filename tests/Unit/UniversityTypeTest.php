<?php

use Tisuchi\UniversityDirectory\Enums\UniversityType;

// US-033: Enum value tests
test('has exactly four cases', function () {
    expect(UniversityType::cases())->toHaveCount(4);
});

test('university case value', function () {
    expect(UniversityType::University->value)->toBe('university');
});

test('college case value', function () {
    expect(UniversityType::College->value)->toBe('college');
});

test('institute case value', function () {
    expect(UniversityType::Institute->value)->toBe('institute');
});

test('academy case value', function () {
    expect(UniversityType::Academy->value)->toBe('academy');
});

test('can create from value', function () {
    expect(UniversityType::from('college'))->toBe(UniversityType::College);
});

test('tryFrom invalid returns null', function () {
    expect(UniversityType::tryFrom('invalid'))->toBeNull();
});

// US-034: Wikidata type mapping tests
test('maps university variants', function () {
    expect(UniversityType::fromWikidata('university'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('public university'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('research university'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('technical university'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('polytechnic'))->toBe(UniversityType::University);
});

test('maps college variants', function () {
    expect(UniversityType::fromWikidata('college'))->toBe(UniversityType::College);
    expect(UniversityType::fromWikidata('community college'))->toBe(UniversityType::College);
    expect(UniversityType::fromWikidata('liberal arts college'))->toBe(UniversityType::College);
});

test('maps institute variants', function () {
    expect(UniversityType::fromWikidata('institute'))->toBe(UniversityType::Institute);
    expect(UniversityType::fromWikidata('institute of technology'))->toBe(UniversityType::Institute);
    expect(UniversityType::fromWikidata('research institute'))->toBe(UniversityType::Institute);
});

test('maps academy variants', function () {
    expect(UniversityType::fromWikidata('academy'))->toBe(UniversityType::Academy);
    expect(UniversityType::fromWikidata('military academy'))->toBe(UniversityType::Academy);
    expect(UniversityType::fromWikidata('art academy'))->toBe(UniversityType::Academy);
});

test('unknown type defaults to university', function () {
    expect(UniversityType::fromWikidata('random string'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('xyz'))->toBe(UniversityType::University);
});

test('mapping is case insensitive', function () {
    expect(UniversityType::fromWikidata('PUBLIC UNIVERSITY'))->toBe(UniversityType::University);
    expect(UniversityType::fromWikidata('Community College'))->toBe(UniversityType::College);
    expect(UniversityType::fromWikidata('INSTITUTE OF TECHNOLOGY'))->toBe(UniversityType::Institute);
});
