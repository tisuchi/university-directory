<?php

namespace Tisuchi\UniversityDirectory\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tisuchi\UniversityDirectory\Services\DataClient;
use Tisuchi\UniversityDirectory\Services\RegionMapping;

class SyncCommand extends Command
{
    protected $signature = 'university-directory:sync
        {countries?* : One or more country codes (e.g. DE US UK)}
        {--region= : Sync all countries in a region}
        {--all : Sync all available countries}
        {--retries=3 : Number of HTTP retry attempts}';

    protected $description = 'Download fresh university data from remote source and update local data files';

    public function __construct(
        private readonly DataClient $dataClient,
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

        $dataPath = $this->dataClient->getDataPath();

        if (! is_dir($dataPath)) {
            mkdir($dataPath, 0755, true);
        }

        $retries = (int) $this->option('retries');
        $succeeded = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar(count($countryCodes));
        $bar->start();

        $results = [];

        foreach ($countryCodes as $code) {
            $code = strtoupper($code);

            try {
                $data = $this->dataClient->fetchRemote($code, $retries);

                $filePath = $dataPath.'/'.$code.'.json';
                $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                file_put_contents($filePath, $json."\n");

                $results[] = [$code, count($data), 'OK'];
                $succeeded++;

                Log::info("Synced {$code}: ".count($data).' universities');
            } catch (\Throwable $e) {
                $results[] = [$code, 0, 'Failed'];
                $failed++;

                $this->error("Failed to sync {$code}: {$e->getMessage()}");
                Log::error("Failed to sync {$code}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Country', 'Universities', 'Status'], $results);
        $this->info("Sync complete. {$succeeded} succeeded, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
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
