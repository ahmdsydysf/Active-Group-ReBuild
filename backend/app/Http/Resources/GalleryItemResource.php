<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GalleryItem',
    description: 'Gallery items (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'gallery_category_id', type: 'integer', nullable: true),
        new OA\Property(property: 'type', type: 'string', enum: ['image', 'video'], nullable: true),
        new OA\Property(property: 'media', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'video_url', type: 'string', nullable: true),
        new OA\Property(property: 'caption', type: 'string', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'GalleryItemAdmin',
    description: 'Gallery items (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'gallery_category_id', type: 'integer', nullable: true),
        new OA\Property(property: 'type', type: 'string', enum: ['image', 'video'], nullable: true),
        new OA\Property(property: 'media', type: 'string', nullable: true),
        new OA\Property(property: 'media_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'video_url', type: 'string', nullable: true),
        new OA\Property(property: 'caption_ar', type: 'string', nullable: true),
        new OA\Property(property: 'caption_en', type: 'string', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'GalleryItemInput',
    required: ['gallery_category_id', 'type'],
    properties: [
        new OA\Property(property: 'gallery_category_id', type: 'integer'),
        new OA\Property(property: 'type', type: 'string', enum: ['image', 'video']),
        new OA\Property(property: 'media', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_media', type: 'boolean'),
        new OA\Property(property: 'video_url', type: 'string'),
        new OA\Property(property: 'caption_ar', type: 'string'),
        new OA\Property(property: 'caption_en', type: 'string'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\GalleryItem */
class GalleryItemResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            
            'gallery_category_id' => $this->gallery_category_id,
            'type' => $this->type,
            'media' => $this->fileUrl('media'),
            'video_url' => $this->video_url,
            'caption' => $this->tr('caption'),
            'category' => new GalleryCategoryResource($this->whenLoaded('category')),
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            'category' => new GalleryCategoryResource($this->whenLoaded('category')),
        ];
    }
}
