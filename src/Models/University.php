<?php

namespace Tisuchi\UniversityDirectory\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tisuchi\UniversityDirectory\Enums\UniversityType;

class University extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Tisuchi\UniversityDirectory\Database\Factories\UniversityFactory::new();
    }

    protected $table = 'ud_universities';

    protected $fillable = [
        'wikidata_id',
        'name',
        'short_name',
        'slug',
        'country_code',
        'type',
        'official_website',
        'aliases',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'type' => UniversityType::class,
        'aliases' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function scopeCountry(Builder $query, string $countryCode): Builder
    {
        return $query->where('country_code', strtoupper($countryCode));
    }

    public function scopeType(Builder $query, UniversityType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'LIKE', '%'.$term.'%')
                ->orWhere('short_name', 'LIKE', '%'.$term.'%')
                ->orWhere('aliases', 'LIKE', '%'.$term.'%');
        });
    }
}
