<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Factory',
    description: 'Factories (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'FactoryAdmin',
    description: 'Factories (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'FactoryInput',
    required: ['name_ar'],
    properties: [
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Factory */
class FactoryResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'name' => $this->tr('name'),
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
