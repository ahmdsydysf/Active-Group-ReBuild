<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Testimonial',
    description: 'Testimonials (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string', nullable: true),
        new OA\Property(property: 'position', type: 'string', nullable: true),
        new OA\Property(property: 'content', type: 'string', nullable: true),
        new OA\Property(property: 'photo', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'given_at', type: 'string', format: 'date', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'TestimonialAdmin',
    description: 'Testimonials (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
        new OA\Property(property: 'position_ar', type: 'string', nullable: true),
        new OA\Property(property: 'position_en', type: 'string', nullable: true),
        new OA\Property(property: 'content_ar', type: 'string', nullable: true),
        new OA\Property(property: 'content_en', type: 'string', nullable: true),
        new OA\Property(property: 'photo', type: 'string', nullable: true),
        new OA\Property(property: 'photo_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'given_at', type: 'string', format: 'date', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'TestimonialInput',
    required: ['name_ar', 'content_ar'],
    properties: [
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'position_ar', type: 'string'),
        new OA\Property(property: 'position_en', type: 'string'),
        new OA\Property(property: 'content_ar', type: 'string'),
        new OA\Property(property: 'content_en', type: 'string'),
        new OA\Property(property: 'photo', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_photo', type: 'boolean'),
        new OA\Property(property: 'given_at', type: 'string', format: 'date'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Testimonial */
class TestimonialResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'name' => $this->tr('name'),
            'position' => $this->tr('position'),
            'content' => $this->tr('content'),
            'photo' => $this->fileUrl('photo'),
            'given_at' => $this->given_at?->toDateString(),
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
