<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Partner;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;

/** @extends ContentController<Partner> */
#[AdminResource('partners', 'Admin · Partners', 'PartnerAdmin')]
#[AdminRecord('partners', 'Admin · Partners', 'PartnerAdmin')]
#[AdminReorder('partners', 'Admin · Partners')]
#[AdminBulk('partners', 'Admin · Partners')]
final class PartnerController extends ContentController
{
    protected string $model = Partner::class;

    protected array $search = ['name_ar', 'name_en'];

    protected array $requiredImages = ['logo'];

    protected function rules(?Model $record): array
    {
        return [
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'field_ar' => 'nullable|string|max:120',
            'field_en' => 'nullable|string|max:120',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'website' => 'nullable|url:https,http|max:500',
            'is_active' => 'nullable|boolean',
        ];
    }
}
