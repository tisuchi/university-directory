<?php

namespace Tisuchi\UniversityDirectory\Services;

class RegionMapping
{
    public const REGIONS = [
        'europe' => [
            'DE', 'FR', 'GB', 'NL', 'BE', 'CH', 'AT', 'SE', 'DK', 'NO', 'FI', 'IE', 'LU', 'IS',
            'IT', 'ES', 'PT', 'PL', 'CZ', 'RO', 'HU', 'UA', 'GR', 'BG', 'HR', 'RS', 'SK', 'SI',
            'LT', 'LV', 'EE', 'MT', 'CY', 'MC', 'LI', 'AD', 'SM', 'VA', 'MK', 'AL', 'BA', 'ME',
            'MD', 'BY', 'RU',
        ],
        'asia' => [
            'CN', 'JP', 'KR', 'IN', 'ID', 'MY', 'TH', 'PH', 'VN', 'SG', 'TW', 'HK', 'PK', 'BD',
            'LK', 'NP', 'MM', 'KH', 'LA', 'MN', 'BN', 'MV', 'AF', 'BT', 'TL', 'KZ', 'UZ', 'GE',
            'AM', 'AZ', 'TM', 'TJ', 'KG',
        ],
        'africa' => [
            'NG', 'ZA', 'KE', 'GH', 'ET', 'TZ', 'UG', 'CM', 'SN', 'RW', 'MZ', 'ZW', 'ZM', 'CI',
            'MG', 'AO', 'CD', 'ML', 'BF', 'NE', 'TD', 'GN', 'BJ', 'TG', 'SL', 'LR', 'MR', 'ER',
            'DJ', 'SO', 'SS', 'NA', 'BW', 'LS', 'SZ', 'MW', 'MU', 'GA', 'CG', 'CF', 'GQ', 'CV',
            'ST', 'KM', 'SC', 'GM', 'GW', 'BI',
        ],
        'americas' => [
            'US', 'CA', 'BR', 'MX', 'AR', 'CO', 'CL', 'PE', 'VE', 'EC', 'BO', 'PY', 'UY', 'CU',
            'DO', 'CR', 'PA', 'GT', 'HN', 'SV', 'NI', 'PR', 'JM', 'TT', 'HT', 'BB', 'BS', 'GY',
            'SR', 'BZ', 'BM', 'KY',
        ],
        'oceania' => [
            'AU', 'NZ', 'PG', 'FJ',
        ],
        'middle-east' => [
            'EG', 'SA', 'AE', 'IR', 'IQ', 'IL', 'JO', 'LB', 'MA', 'TN', 'DZ', 'LY', 'QA', 'KW',
            'BH', 'OM', 'PS', 'SY', 'YE', 'SD', 'TR',
        ],
    ];

    public static function get(string $region): ?array
    {
        return self::REGIONS[$region] ?? null;
    }

    public static function all(): array
    {
        return array_values(array_unique(array_merge(...array_values(self::REGIONS))));
    }

    public static function regions(): array
    {
        return array_keys(self::REGIONS);
    }
}
