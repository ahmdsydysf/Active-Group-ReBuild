<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\TeamMember;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;

/** @extends ContentController<TeamMember> */
#[AdminResource('team', 'Admin · Team', 'TeamMemberAdmin')]
#[AdminRecord('team', 'Admin · Team', 'TeamMemberAdmin')]
#[AdminReorder('team', 'Admin · Team')]
#[AdminBulk('team', 'Admin · Team')]
final class TeamMemberController extends ContentController
{
    protected string $model = TeamMember::class;

    protected array $search = ['name_ar', 'name_en'];

    protected array $requiredImages = ['photo'];

    protected function rules(?Model $record): array
    {
        return [
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'role_ar' => 'nullable|string|max:120',
            'role_en' => 'nullable|string|max:120',
            'is_active' => 'nullable|boolean',
        ];
    }
}
