<?php

namespace Tisuchi\UniversityDirectory\Enums;

enum UniversityType: string
{
    case University = 'university';
    case College = 'college';
    case Institute = 'institute';
    case Academy = 'academy';

    public static function fromWikidata(string $wikidataType): self
    {
        $mapping = [
            'university' => self::University,
            'public university' => self::University,
            'private university' => self::University,
            'research university' => self::University,
            'technical university' => self::University,
            'polytechnic' => self::University,
            'college' => self::College,
            'community college' => self::College,
            'liberal arts college' => self::College,
            'institute' => self::Institute,
            'institute of technology' => self::Institute,
            'research institute' => self::Institute,
            'academy' => self::Academy,
            'military academy' => self::Academy,
            'art academy' => self::Academy,
        ];

        $normalized = strtolower(trim($wikidataType));

        return $mapping[$normalized] ?? self::University;
    }
}
