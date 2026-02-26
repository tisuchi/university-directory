<?php

use Tisuchi\UniversityDirectory\Models\University;

test('shows total count', function () {
    University::factory()->count(10)->create();

    $this->artisan('ud:stats')
        ->expectsOutputToContain('10')
        ->assertExitCode(0);
});

test('shows country count', function () {
    University::factory()->count(2)->create(['country_code' => 'DE']);
    University::factory()->count(2)->create(['country_code' => 'US']);
    University::factory()->count(2)->create(['country_code' => 'GB']);

    $this->artisan('ud:stats')
        ->expectsOutputToContain('3')
        ->assertExitCode(0);
});

test('shows type breakdown', function () {
    University::factory()->count(5)->create(['type' => 'university']);
    University::factory()->count(3)->create(['type' => 'college']);

    $this->artisan('ud:stats')
        ->expectsOutputToContain('university (5)')
        ->assertExitCode(0);

    // Verify both types exist in the database
    expect(University::where('type', 'university')->count())->toBe(5);
    expect(University::where('type', 'college')->count())->toBe(3);
});

test('handles empty database', function () {
    $this->artisan('ud:stats')
        ->expectsOutputToContain('0')
        ->assertExitCode(0);
});
