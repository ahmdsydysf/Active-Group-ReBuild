<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Factory;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;

/** @extends ContentController<Factory> */
#[AdminResource('factories', 'Admin · Factories', 'FactoryAdmin')]
#[AdminRecord('factories', 'Admin · Factories', 'FactoryAdmin')]
#[AdminReorder('factories', 'Admin · Factories')]
#[AdminBulk('factories', 'Admin · Factories')]
final class FactoryController extends ContentController
{
    protected string $model = Factory::class;

    protected array $search = ['name_ar', 'name_en'];

    protected function rules(?Model $record): array
    {
        return [
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'description_ar' => 'nullable|string|max:500',
            'description_en' => 'nullable|string|max:500',
            'address_ar' => 'nullable|string|max:255',
            'address_en' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
