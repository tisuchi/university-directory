<?php

namespace Tisuchi\UniversityDirectory\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'slug' => $this->slug,
            'country_code' => $this->country_code,
            'type' => $this->type?->value,
            'aliases' => $this->aliases,
            'official_website' => $this->official_website,
            'description' => $this->description,
        ];
    }
}
