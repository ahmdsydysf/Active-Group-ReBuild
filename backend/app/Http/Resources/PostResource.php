<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Post',
    description: 'Blog posts (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'title', type: 'string', nullable: true),
        new OA\Property(property: 'excerpt', type: 'string', nullable: true),
        new OA\Property(property: 'body', type: 'string', nullable: true),
        new OA\Property(property: 'cover', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'PostAdmin',
    description: 'Blog posts (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'title_ar', type: 'string', nullable: true),
        new OA\Property(property: 'title_en', type: 'string', nullable: true),
        new OA\Property(property: 'excerpt_ar', type: 'string', nullable: true),
        new OA\Property(property: 'excerpt_en', type: 'string', nullable: true),
        new OA\Property(property: 'body_ar', type: 'string', nullable: true),
        new OA\Property(property: 'body_en', type: 'string', nullable: true),
        new OA\Property(property: 'cover', type: 'string', nullable: true),
        new OA\Property(property: 'cover_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'PostInput',
    required: ['title_ar'],
    properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'title_ar', type: 'string'),
        new OA\Property(property: 'title_en', type: 'string'),
        new OA\Property(property: 'excerpt_ar', type: 'string'),
        new OA\Property(property: 'excerpt_en', type: 'string'),
        new OA\Property(property: 'body_ar', type: 'string'),
        new OA\Property(property: 'body_en', type: 'string'),
        new OA\Property(property: 'cover', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_cover', type: 'boolean'),
        new OA\Property(property: 'published_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Post */
class PostResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->tr('title'),
            'excerpt' => $this->tr('excerpt'),
            'body' => $this->tr('body'),
            'cover' => $this->fileUrl('cover'),
            'published_at' => $this->published_at?->toIso8601String(),
            
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            
        ];
    }
}
