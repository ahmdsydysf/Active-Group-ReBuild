<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Slide',
    description: 'Slides (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'subtitle', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'link_url', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'SlideAdmin',
    description: 'Slides (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'title_ar', type: 'string', nullable: true),
        new OA\Property(property: 'title_en', type: 'string', nullable: true),
        new OA\Property(property: 'subtitle_ar', type: 'string', nullable: true),
        new OA\Property(property: 'subtitle_en', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'link_url', type: 'string', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'SlideInput',
    required: ['title_ar', 'image'],
    properties: [
        new OA\Property(property: 'title_ar', type: 'string'),
        new OA\Property(property: 'title_en', type: 'string'),
        new OA\Property(property: 'subtitle_ar', type: 'string'),
        new OA\Property(property: 'subtitle_en', type: 'string'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_image', type: 'boolean'),
        new OA\Property(property: 'link_url', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Slide */
class SlideResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'title' => $this->tr('title'),
            'subtitle' => $this->tr('subtitle'),
            'image' => $this->fileUrl('image'),
            'link_url' => $this->link_url,
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
