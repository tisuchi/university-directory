<?php

namespace Tisuchi\UniversityDirectory\Services;

use Illuminate\Support\Facades\Http;

class DataClient
{
    public function __construct(
        private string $baseUrl = 'https://raw.githubusercontent.com/tisuchi/university-data/main/data',
    ) {}

    public function fetch(string $countryCode, int $retries = 3): array
    {
        $url = "{$this->baseUrl}/{$countryCode}.json";

        $response = Http::retry($retries, 100)
            ->throw()
            ->get($url);

        return $response->json();
    }
}
