<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ServiceType',
    description: 'Service types (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'name', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ServiceTypeAdmin',
    description: 'Service types (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
        new OA\Property(property: 'description_ar', type: 'string', nullable: true),
        new OA\Property(property: 'description_en', type: 'string', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ServiceTypeInput',
    required: ['name_ar'],
    properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\ServiceType */
class ServiceTypeResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->tr('name'),
            'description' => $this->tr('description'),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'services_count' => $this->whenCounted('services'),
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'services_count' => $this->whenCounted('services'),
        ];
    }
}
