<?php

namespace Tisuchi\UniversityDirectory\Services;

use Illuminate\Support\Str;
use Tisuchi\UniversityDirectory\Enums\UniversityType;
use Tisuchi\UniversityDirectory\Models\University;

class UniversityImporter
{
    public function import(array $universities, string $countryCode, bool $updateExisting = true): array
    {
        $stats = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($universities as $item) {
            $wikidataId = $item['wikidata_id'] ?? null;

            $attributes = [
                'name' => $item['name'],
                'short_name' => $item['short_name'] ?? null,
                'country_code' => strtoupper($countryCode),
                'type' => UniversityType::fromWikidata($item['type'] ?? 'university')->value,
                'official_website' => $item['official_website'] ?? null,
                'aliases' => $item['aliases'] ?? null,
                'latitude' => $item['latitude'] ?? null,
                'longitude' => $item['longitude'] ?? null,
            ];

            if ($wikidataId !== null) {
                $existing = University::where('wikidata_id', $wikidataId)->first();

                if ($existing) {
                    if (! $updateExisting) {
                        $stats['skipped']++;

                        continue;
                    }

                    $existing->update($attributes);
                    $stats['updated']++;

                    continue;
                }
            }

            $attributes['wikidata_id'] = $wikidataId;
            $attributes['slug'] = $this->generateUniqueSlug($item['name'], $countryCode);

            University::create($attributes);
            $stats['created']++;
        }

        return $stats;
    }

    private function generateUniqueSlug(string $name, string $countryCode): string
    {
        $slug = Str::slug($name);

        if (! University::where('slug', $slug)->exists()) {
            return $slug;
        }

        $slug = $slug.'-'.strtolower($countryCode);

        if (! University::where('slug', $slug)->exists()) {
            return $slug;
        }

        $i = 2;
        while (University::where('slug', $slug.'-'.$i)->exists()) {
            $i++;
        }

        return $slug.'-'.$i;
    }
}
