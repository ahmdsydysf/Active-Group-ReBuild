<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProductType',
    description: 'Product types (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_category_id', type: 'integer', nullable: true),
        new OA\Property(property: 'name', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ProductTypeAdmin',
    description: 'Product types (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_category_id', type: 'integer', nullable: true),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
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
    schema: 'ProductTypeInput',
    required: ['product_category_id', 'name_ar'],
    properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_category_id', type: 'integer'),
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_image', type: 'boolean'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\ProductType */
class ProductTypeResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'product_category_id' => $this->product_category_id,
            'name' => $this->tr('name'),
            'description' => $this->tr('description'),
            'image' => $this->fileUrl('image'),
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'products_count' => $this->whenCounted('products'),
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            'category' => new ProductCategoryResource($this->whenLoaded('category')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
