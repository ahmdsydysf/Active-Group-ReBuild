<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Service',
    description: 'Services & advice (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'service_type_id', type: 'integer', nullable: true),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'body', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'video_url', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_ar', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_en', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ServiceAdmin',
    description: 'Services & advice (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'service_type_id', type: 'integer', nullable: true),
        new OA\Property(property: 'title_ar', type: 'string', nullable: true),
        new OA\Property(property: 'title_en', type: 'string', nullable: true),
        new OA\Property(property: 'body_ar', type: 'string', nullable: true),
        new OA\Property(property: 'body_en', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'video_url', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_ar', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_ar_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_en', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_en_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ServiceInput',
    required: ['service_type_id', 'title_ar'],
    properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'service_type_id', type: 'integer'),
        new OA\Property(property: 'title_ar', type: 'string'),
        new OA\Property(property: 'title_en', type: 'string'),
        new OA\Property(property: 'body_ar', type: 'string'),
        new OA\Property(property: 'body_en', type: 'string'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_image', type: 'boolean'),
        new OA\Property(property: 'video_url', type: 'string'),
        new OA\Property(property: 'brochure_ar', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_brochure_ar', type: 'boolean'),
        new OA\Property(property: 'brochure_en', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_brochure_en', type: 'boolean'),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Service */
class ServiceResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'service_type_id' => $this->service_type_id,
            'title' => $this->tr('title'),
            'body' => $this->tr('body'),
            'image' => $this->fileUrl('image'),
            'video_url' => $this->video_url,
            'brochure_ar' => $this->fileUrl('brochure_ar'),
            'brochure_en' => $this->fileUrl('brochure_en'),
            'published_at' => $this->published_at?->toIso8601String(),
            'type' => new ServiceTypeResource($this->whenLoaded('type')),
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            'type' => new ServiceTypeResource($this->whenLoaded('type')),
        ];
    }
}
