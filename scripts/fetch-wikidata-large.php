<?php

/**
 * Fetch university data from Wikidata for large countries.
 * Queries one institution type at a time to avoid timeouts.
 */

$dataDir = __DIR__.'/../data';

$countries = array_map('strtoupper', array_slice($argv, 1));

if (empty($countries)) {
    echo "Usage: php scripts/fetch-wikidata-large.php DE FR GB ...\n";
    exit(1);
}

// Query one Wikidata type at a time
$types = [
    'wd:Q3918'     => 'university',
    'wd:Q875538'   => 'university',       // public university
    'wd:Q902104'   => 'university',       // private university
    'wd:Q23002039' => 'university',       // research university
    'wd:Q1371037'  => 'university',       // technical university
    'wd:Q15936437' => 'university',       // polytechnic
    'wd:Q189004'   => 'college',
    'wd:Q1093910'  => 'college',          // liberal arts college
    'wd:Q1244442'  => 'institute',        // institute of technology
    'wd:Q31855'    => 'institute',        // research institute
    'wd:Q3354859'  => 'academy',          // military academy
    'wd:Q2467461'  => 'academy',          // art academy
    'wd:Q38723'    => 'university',       // higher education institution
    'wd:Q5341295'  => 'university',       // educational organization
];

$succeeded = 0;
$failed = 0;
$total = count($countries);

echo "Fetching large countries (one type at a time)...\n\n";

foreach ($countries as $i => $code) {
    $num = $i + 1;
    echo "[{$num}/{$total}] {$code}:\n";

    $seen = [];
    $universities = [];
    $typeIdx = 0;
    $typeTotal = count($types);
    $anyFailed = false;

    foreach ($types as $wdType => $ourType) {
        $typeIdx++;
        $typeShort = str_replace('wd:', '', $wdType);
        echo "  [{$typeIdx}/{$typeTotal}] {$typeShort}... ";

        try {
            $bindings = fetchType($code, $wdType);
            $newCount = 0;

            foreach ($bindings as $row) {
                $wikidataId = extractQid($row['item']['value'] ?? '');

                if (isset($seen[$wikidataId])) {
                    continue;
                }
                $seen[$wikidataId] = true;
                $newCount++;

                $universities[] = [
                    'wikidata_id' => $wikidataId,
                    'name' => $row['itemLabel']['value'] ?? '',
                    'short_name' => $row['shortName']['value'] ?? null,
                    'official_website' => $row['website']['value'] ?? null,
                    'aliases' => null,
                    'latitude' => isset($row['lat']['value']) ? round((float) $row['lat']['value'], 7) : null,
                    'longitude' => isset($row['lon']['value']) ? round((float) $row['lon']['value'], 7) : null,
                    'type' => $ourType,
                ];
            }

            echo "{$newCount} new (".count($bindings)." total)\n";
        } catch (Exception $e) {
            echo "FAILED: {$e->getMessage()}\n";
            $anyFailed = true;
        }

        usleep(800000); // 800ms between type queries
    }

    if (!empty($universities)) {
        usort($universities, fn($a, $b) => strcasecmp($a['name'], $b['name']));

        $filePath = $dataDir.'/'.$code.'.json';
        $json = json_encode($universities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $json."\n");
        echo "  => {$code}: ".count($universities)." universities saved\n\n";
        $succeeded++;
    } else {
        echo "  => {$code}: no data collected\n\n";
        $failed++;
    }

    if ($i < $total - 1) {
        sleep(2);
    }
}

echo "Done. {$succeeded} succeeded, {$failed} failed.\n";

function fetchType(string $countryCode, string $wdType): array
{
    $query = <<<SPARQL
SELECT DISTINCT ?item ?itemLabel ?shortName ?website ?lat ?lon
WHERE {
  ?item wdt:P31 {$wdType} .
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
            'timeout' => 120,
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

function extractQid(string $uri): string
{
    if (preg_match('/Q\d+$/', $uri, $matches)) {
        return $matches[0];
    }
    return $uri;
}
