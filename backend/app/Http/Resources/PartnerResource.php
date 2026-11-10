<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Partner',
    description: 'Partners (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'logo', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'website', type: 'string', nullable: true),
        new OA\Property(property: 'facebook', type: 'string', nullable: true),
        new OA\Property(property: 'twitter', type: 'string', nullable: true),
        new OA\Property(property: 'instagram', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'PartnerAdmin',
    description: 'Partners (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
        new OA\Property(property: 'description_ar', type: 'string', nullable: true),
        new OA\Property(property: 'description_en', type: 'string', nullable: true),
        new OA\Property(property: 'logo', type: 'string', nullable: true),
        new OA\Property(property: 'logo_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'website', type: 'string', nullable: true),
        new OA\Property(property: 'facebook', type: 'string', nullable: true),
        new OA\Property(property: 'twitter', type: 'string', nullable: true),
        new OA\Property(property: 'instagram', type: 'string', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'PartnerInput',
    required: ['name_ar'],
    properties: [
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'logo', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_logo', type: 'boolean'),
        new OA\Property(property: 'website', type: 'string'),
        new OA\Property(property: 'facebook', type: 'string'),
        new OA\Property(property: 'twitter', type: 'string'),
        new OA\Property(property: 'instagram', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Partner */
class PartnerResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'name' => $this->tr('name'),
            'description' => $this->tr('description'),
            'logo' => $this->fileUrl('logo'),
            'website' => $this->website,
            'facebook' => $this->facebook,
            'twitter' => $this->twitter,
            'instagram' => $this->instagram,
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
