<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Feature',
    description: 'Features (Why us) (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'icon', type: 'string', format: 'uri', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'FeatureAdmin',
    description: 'Features (Why us) (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title_ar', type: 'string', nullable: true),
        new OA\Property(property: 'title_en', type: 'string', nullable: true),
        new OA\Property(property: 'description_ar', type: 'string', nullable: true),
        new OA\Property(property: 'description_en', type: 'string', nullable: true),
        new OA\Property(property: 'icon', type: 'string', nullable: true),
        new OA\Property(property: 'icon_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'FeatureInput',
    required: ['title_ar'],
    properties: [
        new OA\Property(property: 'title_ar', type: 'string'),
        new OA\Property(property: 'title_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'icon', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_icon', type: 'boolean'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Feature */
class FeatureResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'title' => $this->tr('title'),
            'description' => $this->tr('description'),
            'icon' => $this->fileUrl('icon'),
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
