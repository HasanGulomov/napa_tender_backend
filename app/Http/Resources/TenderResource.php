<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'budget' => (float) $this->budget,
            'deadline' => $this->deadline,
            'category' => [
                'id' => $this->categoryId,
                'name' => $this->category?->name ?? 'Noma’lum',
            ],
            'region' => [
                'id' => $this->regionId,
                'name' => $this->region?->name ?? 'Noma’lum',
            ],
            'source' => [
                'id' => $this->sourceId,
                'name' => $this->source?->name ?? 'Noma’lum',
            ],

            'category_name' => $this->category?->name ?? 'Noma’lum',
            'region_name'   => $this->region?->name ?? 'Noma’lum',
            'source_name'   => $this->source?->name ?? 'Noma’lum',

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
