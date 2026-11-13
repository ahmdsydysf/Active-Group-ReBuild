<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Post;
use App\Models\PostCategory;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** @extends ContentController<PostCategory> */
#[AdminResource('post-categories', 'Admin · Articles', 'PostCategoryAdmin', ['section'])]
#[AdminRecord('post-categories', 'Admin · Articles', 'PostCategoryAdmin')]
#[AdminReorder('post-categories', 'Admin · Articles')]
#[AdminBulk('post-categories', 'Admin · Articles')]
final class PostCategoryController extends ContentController
{
    protected string $model = PostCategory::class;

    protected array $search = ['name_ar', 'name_en'];

    protected function rules(?Model $record): array
    {
        return [
            'section' => [$record ? 'sometimes' : 'required', Rule::in(Post::SECTIONS)],
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
        ];
    }

    protected function filter(Builder $query, Request $request): void
    {
        if (in_array($section = $request->query('section'), Post::SECTIONS, true)) {
            $query->where('section', $section);
        }
    }

    protected function present(Model $record): array
    {
        return [...parent::present($record), 'posts_count' => $record->posts()->count()];
    }
}
