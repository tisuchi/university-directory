<?php

/**
 * Enrich university data with Wikipedia article summaries.
 *
 * Two-phase approach:
 *   1. Wikidata SPARQL: resolve QIDs → English Wikipedia article titles
 *   2. Wikipedia Action API: batch-fetch intro summaries (50 per request)
 *
 * Usage: php scripts/fetch-wikipedia-descriptions.php [country_code...]
 *        php scripts/fetch-wikipedia-descriptions.php --all
 *
 * Reads/writes JSON files in data/{CC}.json
 */

$dataDir = __DIR__.'/../data';

if (!is_dir($dataDir)) {
    echo "Data directory not found. Run fetch-wikidata.php first.\n";
    exit(1);
}

// All country codes from RegionMapping
$allCountries = [
    'DE', 'FR', 'GB', 'NL', 'BE', 'CH', 'AT', 'SE', 'DK', 'NO', 'FI', 'IE', 'LU', 'IS',
    'IT', 'ES', 'PT', 'PL', 'CZ', 'RO', 'HU', 'UA', 'GR', 'BG', 'HR', 'RS', 'SK', 'SI',
    'LT', 'LV', 'EE', 'MT', 'CY', 'MC', 'LI', 'AD', 'SM', 'VA', 'MK', 'AL', 'BA', 'ME',
    'MD', 'BY', 'RU',
    'CN', 'JP', 'KR', 'IN', 'ID', 'MY', 'TH', 'PH', 'VN', 'SG', 'TW', 'HK', 'PK', 'BD',
    'LK', 'NP', 'MM', 'KH', 'LA', 'MN', 'BN', 'MV', 'AF', 'BT', 'TL', 'KZ', 'UZ', 'GE',
    'AM', 'AZ', 'TM', 'TJ', 'KG',
    'NG', 'ZA', 'KE', 'GH', 'ET', 'TZ', 'UG', 'CM', 'SN', 'RW', 'MZ', 'ZW', 'ZM', 'CI',
    'MG', 'AO', 'CD', 'ML', 'BF', 'NE', 'TD', 'GN', 'BJ', 'TG', 'SL', 'LR', 'MR', 'ER',
    'DJ', 'SO', 'SS', 'NA', 'BW', 'LS', 'SZ', 'MW', 'MU', 'GA', 'CG', 'CF', 'GQ', 'CV',
    'ST', 'KM', 'SC', 'GM', 'GW', 'BI',
    'US', 'CA', 'BR', 'MX', 'AR', 'CO', 'CL', 'PE', 'VE', 'EC', 'BO', 'PY', 'UY', 'CU',
    'DO', 'CR', 'PA', 'GT', 'HN', 'SV', 'NI', 'PR', 'JM', 'TT', 'HT', 'BB', 'BS', 'GY',
    'SR', 'BZ', 'BM', 'KY',
    'AU', 'NZ', 'PG', 'FJ',
    'EG', 'SA', 'AE', 'IR', 'IQ', 'IL', 'JO', 'LB', 'MA', 'TN', 'DZ', 'LY', 'QA', 'KW',
    'BH', 'OM', 'PS', 'SY', 'YE', 'SD', 'TR',
];

// Parse arguments
$args = array_slice($argv, 1);

if (empty($args)) {
    echo "Usage: php scripts/fetch-wikipedia-descriptions.php [--all | country_code...]\n";
    echo "Example: php scripts/fetch-wikipedia-descriptions.php DE US GB\n";
    echo "         php scripts/fetch-wikipedia-descriptions.php --all\n";
    exit(1);
}

if (in_array('--all', $args)) {
    $countries = $allCountries;
} else {
    $countries = array_map('strtoupper', $args);
}

$totalCountries = count($countries);
$totalEnriched = 0;
$totalSkipped = 0;
$totalMissing = 0;

echo "Enriching university data with Wikipedia descriptions for {$totalCountries} countries...\n\n";

foreach ($countries as $ci => $code) {
    $num = $ci + 1;
    $filePath = $dataDir.'/'.$code.'.json';

    if (!file_exists($filePath)) {
        echo "[{$num}/{$totalCountries}] {$code}... SKIPPED (file not found)\n";
        continue;
    }

    $universities = json_decode(file_get_contents($filePath), true);

    if (empty($universities)) {
        echo "[{$num}/{$totalCountries}] {$code}... SKIPPED (empty)\n";
        continue;
    }

    // Collect QIDs that need descriptions
    $needsDescription = [];
    foreach ($universities as $idx => $uni) {
        if (!empty($uni['description'])) {
            $totalSkipped++;
            continue;
        }
        if (!empty($uni['wikidata_id'])) {
            $needsDescription[$uni['wikidata_id']] = $idx;
        }
    }

    if (empty($needsDescription)) {
        echo "[{$num}/{$totalCountries}] {$code}... SKIPPED (all have descriptions)\n";
        continue;
    }

    echo "[{$num}/{$totalCountries}] {$code}... ";

    // Phase 1: Resolve Wikidata IDs → Wikipedia article titles
    $qidToTitle = resolveWikipediaTitles(array_keys($needsDescription));

    // Phase 2: Batch-fetch summaries from Wikipedia
    $titleToSummary = fetchWikipediaSummaries(array_values($qidToTitle));

    // Merge descriptions back
    $enriched = 0;
    $missing = 0;
    foreach ($needsDescription as $qid => $idx) {
        $title = $qidToTitle[$qid] ?? null;
        $summary = $title ? ($titleToSummary[$title] ?? null) : null;

        if ($summary !== null) {
            $universities[$idx]['description'] = $summary;
            $enriched++;
        } else {
            $universities[$idx]['description'] = null;
            $missing++;
        }
    }

    // Write updated file
    $json = json_encode($universities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($filePath, $json."\n");

    echo "{$enriched} enriched, {$missing} no article\n";
    $totalEnriched += $enriched;
    $totalMissing += $missing;
}

echo "\nDone. {$totalEnriched} enriched, {$totalSkipped} already had descriptions, {$totalMissing} no Wikipedia article.\n";

/**
 * Resolve Wikidata QIDs to English Wikipedia article titles via SPARQL.
 * Batches in groups of 200 to avoid query timeouts.
 */
function resolveWikipediaTitles(array $qids): array
{
    $qidToTitle = [];
    $batches = array_chunk($qids, 200);

    foreach ($batches as $batch) {
        $values = implode(' ', array_map(fn($qid) => "wd:{$qid}", $batch));

        $query = <<<SPARQL
SELECT ?item ?articleTitle WHERE {
  VALUES ?item { {$values} }
  ?article schema:about ?item .
  ?article schema:isPartOf <https://en.wikipedia.org/> .
  ?article schema:name ?articleTitle .
}
SPARQL;

        $url = 'https://query.wikidata.org/sparql?'.http_build_query([
            'query' => $query,
            'format' => 'json',
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: UniversityDirectory/1.0 (https://github.com/tisuchi/university-directory)\r\nAccept: application/sparql-results+json\r\n",
                'timeout' => 120,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            echo "(SPARQL batch failed) ";
            continue;
        }

        $json = json_decode($response, true);
        $bindings = $json['results']['bindings'] ?? [];

        foreach ($bindings as $row) {
            $qid = '';
            if (preg_match('/Q\d+$/', $row['item']['value'] ?? '', $m)) {
                $qid = $m[0];
            }
            $title = $row['articleTitle']['value'] ?? '';
            if ($qid && $title) {
                $qidToTitle[$qid] = $title;
            }
        }

        // Rate limit
        usleep(500000);
    }

    return $qidToTitle;
}

/**
 * Batch-fetch Wikipedia article intro summaries.
 * Uses the Action API with 50 titles per request.
 */
function fetchWikipediaSummaries(array $titles): array
{
    $titleToSummary = [];
    $batches = array_chunk($titles, 50);

    foreach ($batches as $batch) {
        $titlesParam = implode('|', $batch);

        $url = 'https://en.wikipedia.org/w/api.php?'.http_build_query([
            'action' => 'query',
            'prop' => 'extracts',
            'exintro' => 1,
            'explaintext' => 1,
            'exsentences' => 3,
            'titles' => $titlesParam,
            'format' => 'json',
            'formatversion' => 2,
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: UniversityDirectory/1.0 (https://github.com/tisuchi/university-directory)\r\n",
                'timeout' => 30,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            echo "(Wikipedia batch failed) ";
            continue;
        }

        $json = json_decode($response, true);
        $pages = $json['query']['pages'] ?? [];

        foreach ($pages as $page) {
            $title = $page['title'] ?? '';
            $extract = trim($page['extract'] ?? '');

            if ($title && $extract !== '') {
                $titleToSummary[$title] = $extract;
            }
        }

        // Rate limit
        usleep(200000);
    }

    return $titleToSummary;
}
