<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Slide;
use App\OpenApi\AdminCreate;
use App\OpenApi\AdminDelete;
use App\OpenApi\AdminList;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminUpdate;
use Illuminate\Database\Eloquent\Model;

/** @extends ContentController<Slide> */
#[AdminList('slides', 'Admin · Slides', 'SlideAdmin')]
#[AdminCreate('slides', 'Admin · Slides', 'SlideAdmin')]
#[AdminUpdate('slides', 'Admin · Slides', 'SlideAdmin')]
#[AdminDelete('slides', 'Admin · Slides', 'SlideAdmin')]
#[AdminReorder('slides', 'Admin · Slides', 'SlideAdmin')]
final class SlideController extends ContentController
{
    protected string $model = Slide::class;

    protected array $search = ['title_ar', 'title_en'];

    protected array $requiredImages = ['image'];

    protected function rules(?Model $record): array
    {
        return [
            'title_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'title_en' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:500',
            'subtitle_en' => 'nullable|string|max:500',
            'button_ar' => 'nullable|string|max:60',
            'button_en' => 'nullable|string|max:60',
            'button_url' => ['nullable', 'string', 'max:500', 'regex:#^(/|https?://)#'],
            'is_active' => 'nullable|boolean',
        ];
    }
}
