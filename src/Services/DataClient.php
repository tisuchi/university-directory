<?php

namespace Tisuchi\UniversityDirectory\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class DataClient
{
    public function __construct(
        private string $baseUrl = 'https://raw.githubusercontent.com/tisuchi/university-data/main/data',
        private ?string $dataPath = null,
    ) {
        $this->dataPath ??= dirname(__DIR__, 2).'/data';
    }

    public function fetch(string $countryCode, int $retries = 3, bool $remote = false): array
    {
        $countryCode = strtoupper($countryCode);

        return $remote
            ? $this->fetchRemote($countryCode, $retries)
            : $this->fetchLocal($countryCode);
    }

    public function fetchLocal(string $countryCode): array
    {
        $filePath = $this->dataPath.'/'.strtoupper($countryCode).'.json';

        if (! file_exists($filePath)) {
            throw new RuntimeException(
                "Local data file not found for country code '{$countryCode}': {$filePath}. "
                ."Run 'php artisan university-directory:sync {$countryCode}' to download it, "
                .'or use the --remote flag to fetch directly from the remote source.'
            );
        }

        $contents = file_get_contents($filePath);

        if ($contents === false) {
            throw new RuntimeException("Failed to read data file: {$filePath}");
        }

        $data = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                "Invalid JSON in data file {$filePath}: ".json_last_error_msg()
            );
        }

        return $data;
    }

    public function fetchRemote(string $countryCode, int $retries = 3): array
    {
        $url = "{$this->baseUrl}/".strtoupper($countryCode).'.json';

        $response = Http::retry($retries, 100)
            ->throw()
            ->get($url);

        return $response->json();
    }

    public function getDataPath(): string
    {
        return $this->dataPath;
    }
}
