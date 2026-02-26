<?php

namespace Tisuchi\UniversityDirectory\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tisuchi\UniversityDirectory\Services\DataClient;
use Tisuchi\UniversityDirectory\Services\RegionMapping;
use Tisuchi\UniversityDirectory\Services\UniversityImporter;

class ImportCommand extends Command
{
    protected $signature = 'university-directory:import
        {countries?* : One or more country codes (e.g. DE US UK)}
        {--region= : Import all countries in a region (europe, asia, africa, americas, oceania, middle-east)}
        {--all : Import all available countries}
        {--chunk=500 : Number of records to process per batch}
        {--retries=3 : Number of HTTP retry attempts}
        {--no-update : Skip updating existing records}';

    protected $description = 'Import universities from the curated data source';

    public function __construct(
        private readonly DataClient $dataClient,
        private readonly UniversityImporter $importer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $countryCodes = $this->resolveCountryCodes();

        if ($countryCodes === null) {
            return self::FAILURE;
        }

        if (count($countryCodes) === 0) {
            $this->error('Provide country codes, --region, or --all.');

            return self::FAILURE;
        }

        if ($this->option('all') && ! $this->option('no-interaction')) {
            if (! $this->confirm('Import data for '.count($countryCodes).' countries?')) {
                return self::SUCCESS;
            }
        }

        $retries = (int) $this->option('retries');
        $updateExisting = ! $this->option('no-update');
        $startTime = microtime(true);

        $results = [];
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        $bar = $this->output->createProgressBar(count($countryCodes));
        $bar->start();

        foreach ($countryCodes as $code) {
            $code = strtoupper($code);
            Log::info("Starting university import for {$code}");

            try {
                $data = $this->dataClient->fetch($code, $retries);
                $stats = $this->importer->import($data, $code, $updateExisting);

                $totalCreated += $stats['created'];
                $totalUpdated += $stats['updated'];
                $totalSkipped += $stats['skipped'];

                $results[] = [$code, $stats['created'], $stats['updated'], $stats['skipped'], 'OK'];

                Log::info("Imported {$code}: {$stats['created']} created, {$stats['updated']} updated, {$stats['skipped']} skipped");
            } catch (\Throwable $e) {
                $results[] = [$code, 0, 0, 0, 'Failed'];
                $this->error("Failed to import {$code}: {$e->getMessage()}");
                Log::error("Failed to import {$code}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Country', 'Created', 'Updated', 'Skipped', 'Status'], $results);

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->info("Done. Total: {$totalCreated} created, {$totalUpdated} updated, {$totalSkipped} skipped. Time: {$elapsed}s");

        Log::info("Import complete: {$totalCreated} created, {$totalUpdated} updated across ".count($countryCodes).' countries');

        return self::SUCCESS;
    }

    private function resolveCountryCodes(): ?array
    {
        $codes = $this->argument('countries') ?: [];

        if ($this->option('all')) {
            return RegionMapping::all();
        }

        if ($region = $this->option('region')) {
            $regionCodes = RegionMapping::get($region);

            if ($regionCodes === null) {
                $this->error("Unknown region: {$region}. Valid regions: ".implode(', ', RegionMapping::regions()));

                return null;
            }

            $codes = array_unique(array_merge($codes, $regionCodes));
        }

        return array_values($codes);
    }
}
