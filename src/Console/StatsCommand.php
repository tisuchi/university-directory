<?php

namespace Tisuchi\UniversityDirectory\Console;

use Illuminate\Console\Command;
use Tisuchi\UniversityDirectory\Models\University;

class StatsCommand extends Command
{
    protected $signature = 'university-directory:stats';

    protected $description = 'Show university database statistics';

    public function handle(): int
    {
        $total = University::count();
        $countries = University::distinct('country_code')->count('country_code');

        $types = University::query()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $typeBreakdown = $types->map(fn ($count, $type) => "{$type} (".number_format($count).')')->implode(', ');

        $topCountries = University::query()
            ->selectRaw('country_code, count(*) as count')
            ->groupBy('country_code')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'country_code');

        $topCountriesStr = $topCountries->map(fn ($count, $code) => "{$code} (".number_format($count).')')->implode(', ');

        $lastUpdated = University::max('updated_at') ?? 'N/A';

        $this->line('');
        $this->line('University Directory Stats');
        $this->line('--------------------------');
        $this->line('Total universities: '.number_format($total));
        $this->line("Countries:          {$countries}");
        $this->line("Types:              {$typeBreakdown}");
        $this->line("Top countries:      {$topCountriesStr}");
        $this->line("Last updated:       {$lastUpdated}");
        $this->line('');

        return self::SUCCESS;
    }
}
