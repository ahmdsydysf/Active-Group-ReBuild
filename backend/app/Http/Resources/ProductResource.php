<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    description: 'Products (public, localized by the `lang` query or Accept-Language header)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_type_id', type: 'integer', nullable: true),
        new OA\Property(property: 'factory_id', type: 'integer', nullable: true),
        new OA\Property(property: 'partner_id', type: 'integer', nullable: true),
        new OA\Property(property: 'name', type: 'string', nullable: true),
        new OA\Property(property: 'description', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_ar', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_en', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'is_featured', type: 'boolean', nullable: true),
    ]
)]
#[OA\Schema(
    schema: 'ProductAdmin',
    description: 'Products (admin, all languages)',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_type_id', type: 'integer', nullable: true),
        new OA\Property(property: 'factory_id', type: 'integer', nullable: true),
        new OA\Property(property: 'partner_id', type: 'integer', nullable: true),
        new OA\Property(property: 'name_ar', type: 'string', nullable: true),
        new OA\Property(property: 'name_en', type: 'string', nullable: true),
        new OA\Property(property: 'description_ar', type: 'string', nullable: true),
        new OA\Property(property: 'description_en', type: 'string', nullable: true),
        new OA\Property(property: 'image', type: 'string', nullable: true),
        new OA\Property(property: 'image_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_ar', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_ar_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'brochure_en', type: 'string', nullable: true),
        new OA\Property(property: 'brochure_en_url', type: 'string', format: 'uri', nullable: true),
        new OA\Property(property: 'is_featured', type: 'boolean', nullable: true),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['product_type_id', 'name_ar'],
    properties: [
        new OA\Property(property: 'slug', type: 'string'),
        new OA\Property(property: 'product_type_id', type: 'integer'),
        new OA\Property(property: 'factory_id', type: 'integer'),
        new OA\Property(property: 'partner_id', type: 'integer'),
        new OA\Property(property: 'name_ar', type: 'string'),
        new OA\Property(property: 'name_en', type: 'string'),
        new OA\Property(property: 'description_ar', type: 'string'),
        new OA\Property(property: 'description_en', type: 'string'),
        new OA\Property(property: 'image', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_image', type: 'boolean'),
        new OA\Property(property: 'brochure_ar', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_brochure_ar', type: 'boolean'),
        new OA\Property(property: 'brochure_en', type: 'string', format: 'binary'),
        new OA\Property(property: 'remove_brochure_en', type: 'boolean'),
        new OA\Property(property: 'is_featured', type: 'boolean'),
        new OA\Property(property: 'sort_order', type: 'integer'),
        new OA\Property(property: 'is_active', type: 'boolean'),
    ]
)]
/** @mixin \App\Models\Product */
class ProductResource extends BaseResource
{
    protected function publicArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'product_type_id' => $this->product_type_id,
            'factory_id' => $this->factory_id,
            'partner_id' => $this->partner_id,
            'name' => $this->tr('name'),
            'description' => $this->tr('description'),
            'image' => $this->fileUrl('image'),
            'brochure_ar' => $this->fileUrl('brochure_ar'),
            'brochure_en' => $this->fileUrl('brochure_en'),
            'is_featured' => $this->is_featured,
            'type' => new ProductTypeResource($this->whenLoaded('type')),
            'factory' => new FactoryResource($this->whenLoaded('factory')),
            'partner' => new PartnerResource($this->whenLoaded('partner')),
        ];
    }

    protected function adminRelations(Request $request): array
    {
        return [
            'type' => new ProductTypeResource($this->whenLoaded('type')),
            'factory' => new FactoryResource($this->whenLoaded('factory')),
            'partner' => new PartnerResource($this->whenLoaded('partner')),
        ];
    }
}
