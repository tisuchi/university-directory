<?php

namespace Tisuchi\UniversityDirectory\Console;

use Illuminate\Console\Command;
use Tisuchi\UniversityDirectory\Enums\UniversityType;
use Tisuchi\UniversityDirectory\Models\University;

class ListCommand extends Command
{
    protected $signature = 'university-directory:list
        {--country= : Filter by country code}
        {--search= : Search by name}
        {--type= : Filter by type}
        {--limit=20 : Number of results}';

    protected $description = 'List universities in the database';

    public function handle(): int
    {
        $query = University::query();

        if ($country = $this->option('country')) {
            $query->country($country);
        }

        if ($search = $this->option('search')) {
            $query->search($search);
        }

        if ($type = $this->option('type')) {
            $enumType = UniversityType::tryFrom($type);

            if ($enumType) {
                $query->type($enumType);
            }
        }

        $total = $query->count();
        $limit = (int) $this->option('limit');
        $universities = $query->limit($limit)->get();

        $rows = $universities->map(fn ($u) => [
            $u->id,
            $u->name,
            $u->short_name,
            $u->country_code,
            $u->type?->value,
            $u->slug,
        ])->toArray();

        $this->table(['ID', 'Name', 'Short Name', 'Country', 'Type', 'Slug'], $rows);
        $this->info("Showing {$universities->count()} of {$total} universities.");

        return self::SUCCESS;
    }
}
