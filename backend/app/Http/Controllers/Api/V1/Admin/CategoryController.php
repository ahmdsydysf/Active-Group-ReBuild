<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\ProductCategory;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<ProductCategory> */
#[AdminResource('product-categories', 'Admin · Product categories', 'CategoryAdmin')]
#[AdminRecord('product-categories', 'Admin · Product categories', 'CategoryAdmin')]
#[AdminReorder('product-categories', 'Admin · Product categories')]
#[AdminBulk('product-categories', 'Admin · Product categories')]
final class CategoryController extends ContentController
{
    protected string $model = ProductCategory::class;

    protected array $search = ['name_ar', 'name_en'];

    protected array $with = ['types'];

    protected function rules(?Model $record): array
    {
        return [
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9-]+$/', Rule::unique('product_categories', 'slug')->ignore($record)],
            'color' => ['nullable', Rule::in(ProductCategory::COLORS)],
            'is_active' => 'nullable|boolean',
        ];
    }

    /** Each category comes with its types and product counts for the tree. */
    protected function present(Model $record): array
    {
        $record->loadMissing('types')->types->loadCount('products');

        return [
            ...parent::present($record),
            'products_count' => $record->types->sum('products_count'),
            'types' => $record->types->sortBy('sort_order')->values()->map(fn ($t) => [
                'id' => $t->id, 'name_ar' => $t->name_ar, 'name_en' => $t->name_en, 'slug' => $t->slug,
                'is_active' => $t->is_active, 'products_count' => $t->products_count,
            ]),
        ];
    }
}
