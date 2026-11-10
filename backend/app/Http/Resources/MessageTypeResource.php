<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MessageType',
    description: 'Message types (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'MessageTypeAdmin',
    description: 'Message types (admin, all languages)',
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
    schema: 'MessageTypeInput',
    required: ['name_ar'],
    properties: [
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\MessageType */
class MessageTypeResource extends BaseResource
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
