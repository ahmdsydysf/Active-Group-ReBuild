<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Service;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<Service> */
#[AdminResource('services', 'Admin · Services', 'ServiceAdmin')]
#[AdminRecord('services', 'Admin · Services', 'ServiceAdmin')]
#[AdminReorder('services', 'Admin · Services')]
#[AdminBulk('services', 'Admin · Services')]
final class ServiceController extends ContentController
{
    protected string $model = Service::class;

    protected array $search = ['title_ar', 'title_en'];

    protected array $requiredImages = ['image'];

    protected function rules(?Model $record): array
    {
        return [
            'title_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'title_en' => 'nullable|string|max:120',
            'summary_ar' => 'nullable|string|max:500',
            'summary_en' => 'nullable|string|max:500',
            'icon' => ['nullable', Rule::in(Service::ICONS)],
            'is_active' => 'nullable|boolean',
        ];
    }
}
