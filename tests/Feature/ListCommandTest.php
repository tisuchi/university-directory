<?php

use Tisuchi\UniversityDirectory\Models\University;

test('lists universities', function () {
    University::factory()->create(['name' => 'Munich University']);
    University::factory()->create(['name' => 'Berlin University']);

    $this->artisan('university-directory:list')
        ->expectsOutputToContain('Munich University')
        ->expectsOutputToContain('Berlin University')
        ->assertExitCode(0);
});

test('filters by country', function () {
    University::factory()->count(3)->create(['country_code' => 'DE']);
    University::factory()->count(2)->create(['country_code' => 'US']);

    $this->artisan('university-directory:list', ['--country' => 'DE'])
        ->expectsOutputToContain('3 of 3')
        ->assertExitCode(0);
});

test('filters by search', function () {
    University::factory()->create(['name' => 'Technical University of Munich']);
    University::factory()->create(['name' => 'Harvard University']);

    $this->artisan('university-directory:list', ['--search' => 'Munich'])
        ->expectsOutputToContain('Munich')
        ->assertExitCode(0);
});

test('respects limit', function () {
    University::factory()->count(30)->create();

    $this->artisan('university-directory:list', ['--limit' => '5'])
        ->expectsOutputToContain('5 of 30')
        ->assertExitCode(0);
});

test('shows message when empty', function () {
    $this->artisan('university-directory:list')
        ->expectsOutputToContain('0 of 0')
        ->assertExitCode(0);
});

test('filters by type', function () {
    University::factory()->count(3)->create(['type' => 'college']);
    University::factory()->count(2)->create(['type' => 'university']);

    $this->artisan('university-directory:list', ['--type' => 'college'])
        ->expectsOutputToContain('3 of 3')
        ->assertExitCode(0);
});
