<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Product;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** @extends ContentController<Product> */
#[AdminResource('products', 'Admin · Products', 'ProductAdmin', ['category', 'factory', 'status'])]
#[AdminRecord('products', 'Admin · Products', 'ProductAdmin')]
#[AdminReorder('products', 'Admin · Products')]
#[AdminBulk('products', 'Admin · Products')]
final class ProductController extends ContentController
{
    protected string $model = Product::class;

    protected array $search = ['name_ar', 'name_en', 'active_ingredient_ar', 'active_ingredient_en'];

    protected array $with = ['type.category', 'factory'];

    protected array $jsonFields = ['doses_ar', 'doses_en', 'faqs_ar', 'faqs_en'];

    protected function rules(?Model $record): array
    {
        $text = fn (int $max) => "nullable|string|max:{$max}";
        $rows = [];
        foreach (['ar', 'en'] as $l) {
            $rows += [
                "doses_{$l}" => 'nullable|array|max:40',
                "doses_{$l}.*.crop" => 'required|string|max:80',
                "doses_{$l}.*.pest" => $text(120),
                "doses_{$l}.*.rate" => $text(120),
                "doses_{$l}.*.interval" => $text(80),
                "doses_{$l}.*.phi" => $text(80),
                "faqs_{$l}" => 'nullable|array|max:20',
                "faqs_{$l}.*.q" => 'required|string|max:200',
                "faqs_{$l}.*.a" => 'required|string|max:1000',
            ];
        }

        return [
            'product_type_id' => [$record ? 'sometimes' : 'required', 'integer', 'exists:product_types,id'],
            'factory_id' => 'nullable|integer|exists:factories,id',
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9-]+$/', Rule::unique('products', 'slug')->ignore($record)],
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:160'],
            'name_en' => $text(160),
            'active_ingredient_ar' => $text(255), 'active_ingredient_en' => $text(255),
            'formulation_ar' => $text(255), 'formulation_en' => $text(255),
            'summary_ar' => $text(500), 'summary_en' => $text(500),
            'body_ar' => $text(60000), 'body_en' => $text(60000),
            'packs_ar' => $text(255), 'packs_en' => $text(255),
            'registration_no' => $text(60),
            'safety_ar' => $text(2000), 'safety_en' => $text(2000),
            'seo_title_ar' => $text(70), 'seo_title_en' => $text(70),
            'seo_description_ar' => $text(170), 'seo_description_en' => $text(170),
            'in_stock' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            ...$rows,
        ];
    }

    protected function order(Builder $query): void
    {
        $query->latest('updated_at');
    }

    protected function filter(Builder $query, Request $request): void
    {
        if ($category = $request->integer('category')) {
            $query->whereHas('type', fn (Builder $q) => $q->where('product_category_id', $category));
        }
        if ($factory = $request->integer('factory')) {
            $query->where('factory_id', $factory);
        }
        match ($request->query('status')) {
            'active' => $query->where('is_active', true),
            'hidden' => $query->where('is_active', false),
            'missing_en' => $query->where(fn (Builder $q) => $q->whereNull('name_en')->orWhere('name_en', '')),
            default => null,
        };
    }

    protected function present(Model $record): array
    {
        /** @var Product $record */
        return [
            ...parent::present($record),
            'category' => $record->type?->category ? ['id' => $record->type->category->id, 'name_ar' => $record->type->category->name_ar, 'color' => $record->type->category->color] : null,
            'type_name' => $record->type?->name_ar,
            'factory_name' => $record->factory?->name_ar,
        ];
    }
}
