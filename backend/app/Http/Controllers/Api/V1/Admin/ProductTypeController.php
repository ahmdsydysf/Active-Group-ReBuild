<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\ProductType;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<ProductType> */
#[AdminResource('product-types', 'Admin · Product categories', 'ProductTypeAdmin')]
#[AdminRecord('product-types', 'Admin · Product categories', 'ProductTypeAdmin')]
#[AdminReorder('product-types', 'Admin · Product categories')]
#[AdminBulk('product-types', 'Admin · Product categories')]
final class ProductTypeController extends ContentController
{
    protected string $model = ProductType::class;

    protected array $search = ['name_ar', 'name_en'];

    protected function rules(?Model $record): array
    {
        return [
            'product_category_id' => [$record ? 'sometimes' : 'required', 'integer', 'exists:product_categories,id'],
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/', Rule::unique('product_types', 'slug')->ignore($record)],
            'is_active' => 'nullable|boolean',
        ];
    }
}
