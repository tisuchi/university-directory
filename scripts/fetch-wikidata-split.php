<?php

/**
 * Fetch university data from Wikidata for large countries.
 * Splits queries by institution type to avoid Wikidata timeouts.
 */

$dataDir = __DIR__.'/../data';

$countries = array_map('strtoupper', array_slice($argv, 1));

if (empty($countries)) {
    echo "Usage: php scripts/fetch-wikidata-split.php DE FR GB ...\n";
    exit(1);
}

// Split into batches of types to keep queries manageable
$typeBatches = [
    ['wd:Q3918', 'wd:Q875538', 'wd:Q902104'],          // university, public univ, private univ
    ['wd:Q23002039', 'wd:Q1371037', 'wd:Q15936437'],    // research univ, technical univ, polytechnic
    ['wd:Q189004', 'wd:Q1093910'],                       // college, liberal arts college
    ['wd:Q1244442', 'wd:Q31855'],                        // institute of tech, research institute
    ['wd:Q3354859', 'wd:Q2467461'],                      // military academy, art academy
    ['wd:Q38723', 'wd:Q5341295'],                        // higher ed institution, educational org
];

$succeeded = 0;
$failed = 0;
$total = count($countries);

echo "Fetching large countries (split by type batches)...\n\n";

foreach ($countries as $i => $code) {
    $num = $i + 1;
    echo "[{$num}/{$total}] {$code}... ";

    $allResults = [];

    try {
        foreach ($typeBatches as $batchIdx => $types) {
            $batchNum = $batchIdx + 1;
            $batchTotal = count($typeBatches);
            echo "batch {$batchNum}/{$batchTotal} ";

            $results = fetchBatch($code, $types);
            $allResults = array_merge($allResults, $results);

            // Rate limit between batches
            usleep(1000000); // 1 second
        }

        // Deduplicate and sort
        $data = deduplicateAndSort($allResults);

        $filePath = $dataDir.'/'.$code.'.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $json."\n");
        echo "=> ".count($data)." universities\n";
        $succeeded++;
    } catch (Exception $e) {
        echo "FAILED: {$e->getMessage()}\n";
        $filePath = $dataDir.'/'.$code.'.json';
        file_put_contents($filePath, "[]\n");
        $failed++;
    }

    // Rate limit between countries
    if ($i < $total - 1) {
        sleep(2);
    }
}

echo "\nDone. {$succeeded} succeeded, {$failed} failed.\n";

function fetchBatch(string $countryCode, array $types): array
{
    $typeValues = implode(' ', $types);

    $query = <<<SPARQL
SELECT DISTINCT ?item ?itemLabel ?shortName ?website ?typeLabel ?lat ?lon
WHERE {
  VALUES ?type { {$typeValues} }
  ?item wdt:P31 ?type .
  ?item wdt:P17 ?country .
  ?country wdt:P297 "{$countryCode}" .

  OPTIONAL { ?item wdt:P856 ?website . }
  OPTIONAL { ?item wdt:P1813 ?shortName . FILTER(LANG(?shortName) = "en") }
  OPTIONAL {
    ?item p:P625 ?coordStatement .
    ?coordStatement psv:P625 ?coordNode .
    ?coordNode wikibase:geoLatitude ?lat .
    ?coordNode wikibase:geoLongitude ?lon .
  }

  SERVICE wikibase:label { bd:serviceParam wikibase:language "en" . }
}
SPARQL;

    $url = 'https://query.wikidata.org/sparql';

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "User-Agent: UniversityDirectory/1.0 (https://github.com/tisuchi/university-directory)\r\nAccept: application/sparql-results+json\r\nContent-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query(['query' => $query]),
            'timeout' => 180,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception('HTTP request failed');
    }

    $json = json_decode($response, true);

    if (!isset($json['results']['bindings'])) {
        throw new Exception('Unexpected response format');
    }

    return $json['results']['bindings'];
}

function deduplicateAndSort(array $bindings): array
{
    $seen = [];
    $universities = [];

    foreach ($bindings as $row) {
        $wikidataId = extractQid($row['item']['value'] ?? '');

        if (isset($seen[$wikidataId])) {
            continue;
        }
        $seen[$wikidataId] = true;

        $universities[] = [
            'wikidata_id' => $wikidataId,
            'name' => $row['itemLabel']['value'] ?? '',
            'short_name' => $row['shortName']['value'] ?? null,
            'official_website' => $row['website']['value'] ?? null,
            'aliases' => null,
            'latitude' => isset($row['lat']['value']) ? round((float) $row['lat']['value'], 7) : null,
            'longitude' => isset($row['lon']['value']) ? round((float) $row['lon']['value'], 7) : null,
            'type' => mapType($row['typeLabel']['value'] ?? 'university'),
        ];
    }

    usort($universities, fn($a, $b) => strcasecmp($a['name'], $b['name']));

    return $universities;
}

function extractQid(string $uri): string
{
    if (preg_match('/Q\d+$/', $uri, $matches)) {
        return $matches[0];
    }
    return $uri;
}

function mapType(string $typeLabel): string
{
    $label = strtolower(trim($typeLabel));

    $mapping = [
        'university' => 'university',
        'public university' => 'university',
        'private university' => 'university',
        'research university' => 'university',
        'technical university' => 'university',
        'polytechnic' => 'university',
        'higher education institution' => 'university',
        'educational organization' => 'university',
        'college' => 'college',
        'community college' => 'college',
        'liberal arts college' => 'college',
        'institute of technology' => 'institute',
        'research institute' => 'institute',
        'military academy' => 'academy',
        'art academy' => 'academy',
    ];

    return $mapping[$label] ?? 'university';
}
