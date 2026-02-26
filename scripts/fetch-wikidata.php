<?php

/**
 * Fetch university data from Wikidata SPARQL endpoint.
 *
 * Usage: php scripts/fetch-wikidata.php [country_code...]
 *        php scripts/fetch-wikidata.php --all
 *
 * Writes JSON files to data/{CC}.json
 */

$dataDir = __DIR__.'/../data';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
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
    echo "Usage: php scripts/fetch-wikidata.php [--all | country_code...]\n";
    echo "Example: php scripts/fetch-wikidata.php DE US GB\n";
    echo "         php scripts/fetch-wikidata.php --all\n";
    exit(1);
}

if (in_array('--all', $args)) {
    $countries = $allCountries;
} else {
    $countries = array_map('strtoupper', $args);
}

$succeeded = 0;
$failed = 0;
$total = count($countries);

echo "Fetching university data from Wikidata for {$total} countries...\n\n";

foreach ($countries as $i => $code) {
    $num = $i + 1;
    echo "[{$num}/{$total}] {$code}... ";

    try {
        $data = fetchCountry($code);
        $filePath = $dataDir.'/'.$code.'.json';
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $json."\n");
        echo count($data)." universities\n";
        $succeeded++;
    } catch (Exception $e) {
        echo "FAILED: {$e->getMessage()}\n";
        // Write empty array for countries with no data
        $filePath = $dataDir.'/'.$code.'.json';
        file_put_contents($filePath, "[]\n");
        $failed++;
    }

    // Rate limit: be respectful to Wikidata
    if ($i < $total - 1) {
        usleep(500000); // 500ms between requests
    }
}

echo "\nDone. {$succeeded} succeeded, {$failed} failed.\n";

function fetchCountry(string $countryCode): array
{
    $query = buildQuery($countryCode);

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
        throw new Exception('HTTP request failed');
    }

    $json = json_decode($response, true);

    if (!isset($json['results']['bindings'])) {
        throw new Exception('Unexpected response format');
    }

    return processResults($json['results']['bindings']);
}

function buildQuery(string $countryCode): string
{
    return <<<SPARQL
SELECT DISTINCT ?item ?itemLabel ?shortName ?website ?typeLabel ?lat ?lon
  (GROUP_CONCAT(DISTINCT ?altLabel; separator="|") AS ?aliases)
WHERE {
  VALUES ?type {
    wd:Q3918
    wd:Q875538
    wd:Q902104
    wd:Q23002039
    wd:Q1371037
    wd:Q15936437
    wd:Q189004
    wd:Q1093910
    wd:Q1244442
    wd:Q31855
    wd:Q3354859
    wd:Q2467461
    wd:Q38723
    wd:Q5341295
  }
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
  OPTIONAL { ?item skos:altLabel ?altLabel . FILTER(LANG(?altLabel) = "en") }

  SERVICE wikibase:label { bd:serviceParam wikibase:language "en" . }
}
GROUP BY ?item ?itemLabel ?shortName ?website ?typeLabel ?lat ?lon
SPARQL;
}

function processResults(array $bindings): array
{
    $seen = [];
    $universities = [];

    foreach ($bindings as $row) {
        $wikidataId = extractQid($row['item']['value'] ?? '');

        // Deduplicate by wikidata ID (multiple types can cause duplicates)
        if (isset($seen[$wikidataId])) {
            continue;
        }
        $seen[$wikidataId] = true;

        $aliases = [];
        if (!empty($row['aliases']['value'])) {
            $aliases = array_values(array_unique(
                array_filter(explode('|', $row['aliases']['value']))
            ));
        }

        $universities[] = [
            'wikidata_id' => $wikidataId,
            'name' => $row['itemLabel']['value'] ?? '',
            'short_name' => $row['shortName']['value'] ?? null,
            'official_website' => $row['website']['value'] ?? null,
            'aliases' => !empty($aliases) ? $aliases : null,
            'latitude' => isset($row['lat']['value']) ? round((float) $row['lat']['value'], 7) : null,
            'longitude' => isset($row['lon']['value']) ? round((float) $row['lon']['value'], 7) : null,
            'type' => mapType($row['typeLabel']['value'] ?? 'university'),
        ];
    }

    // Sort by name for consistent output
    usort($universities, fn($a, $b) => strcasecmp($a['name'], $b['name']));

    return $universities;
}

function extractQid(string $uri): string
{
    // Extract Q-ID from URI like http://www.wikidata.org/entity/Q49108
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
