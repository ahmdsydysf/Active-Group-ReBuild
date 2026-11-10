<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Contract',
    description: 'Contracts (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ContractAdmin',
    description: 'Contracts (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title_ar', type: 'string', nullable: true),
        new OA\Property(property: 'title_en', type: 'string', nullable: true),
        new OA\Property(property: 'description_ar', type: 'string', nullable: true),
        new OA\Property(property: 'description_en', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ContractInput',
    required: ['title_ar'],
    properties: [
        new OA\Property(property: 'title_ar', type: 'string'),
        new OA\Property(property: 'title_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_image', type: 'boolean'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Contract */
class ContractResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'title' => $this->tr('title'),
            'description' => $this->tr('description'),
            'image' => $this->fileUrl('image'),
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
