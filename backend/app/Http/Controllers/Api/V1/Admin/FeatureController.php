<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Feature;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<Feature> */
#[AdminResource('features', 'Admin · Why us', 'FeatureAdmin')]
#[AdminRecord('features', 'Admin · Why us', 'FeatureAdmin')]
#[AdminReorder('features', 'Admin · Why us')]
#[AdminBulk('features', 'Admin · Why us')]
final class FeatureController extends ContentController
{
    protected string $model = Feature::class;

    protected array $search = ['title_ar', 'title_en'];

    protected function rules(?Model $record): array
    {
        return [
            'title_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'title_en' => 'nullable|string|max:120',
            'description_ar' => 'nullable|string|max:300',
            'description_en' => 'nullable|string|max:300',
            'icon' => ['nullable', Rule::in(Feature::ICONS)],
            'color' => ['nullable', Rule::in(Feature::COLORS)],
            'is_active' => 'nullable|boolean',
        ];
    }
}
