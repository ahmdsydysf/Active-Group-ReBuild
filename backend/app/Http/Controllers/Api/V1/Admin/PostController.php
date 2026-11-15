<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Post;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Articles for both الخبرات والنصائح (section=advice) and المدونة (section=blog). @extends ContentController<Post> */
#[AdminResource('posts', 'Admin · Articles', 'PostAdmin', ['section', 'category', 'status'])]
#[AdminRecord('posts', 'Admin · Articles', 'PostAdmin')]
#[AdminBulk('posts', 'Admin · Articles')]
final class PostController extends ContentController
{
    protected string $model = Post::class;

    protected array $search = ['title_ar', 'title_en'];

    protected array $with = ['category'];

    protected array $requiredImages = ['image'];

    protected function rules(?Model $record): array
    {
        $required = $record ? 'sometimes' : 'required';

        return [
            'section' => [$required, Rule::in(Post::SECTIONS)],
            'post_category_id' => 'nullable|integer|exists:post_categories,id',
            'author_id' => 'nullable|integer|exists:team_members,id',
            'slug' => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9-]+$/', Rule::unique('posts', 'slug')->ignore($record)],
            'title_ar' => [$required, 'string', 'max:200'],
            'title_en' => 'nullable|string|max:200',
            'excerpt_ar' => 'nullable|string|max:500',
            'excerpt_en' => 'nullable|string|max:500',
            'body_ar' => 'nullable|string|max:100000',
            'body_en' => 'nullable|string|max:100000',
            'seo_description_ar' => 'nullable|string|max:170',
            'seo_description_en' => 'nullable|string|max:170',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ];
    }

    protected function order(Builder $query): void
    {
        $query->orderByRaw('published_at is null desc')->latest('published_at')->latest('id');
    }

    protected function filter(Builder $query, Request $request): void
    {
        if (in_array($section = $request->query('section'), Post::SECTIONS, true)) {
            $query->where('section', $section);
        }
        if ($category = $request->integer('category')) {
            $query->where('post_category_id', $category);
        }
        match ($request->query('status')) {
            'draft' => $query->where('is_active', false),
            'scheduled' => $query->where('is_active', true)->where('published_at', '>', now()),
            'published' => $query->where('is_active', true)->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now())),
            default => null,
        };
    }

    protected function present(Model $record): array
    {
        /** @var Post $record */
        return [...parent::present($record), 'status' => $record->status(), 'category_name' => $record->category?->name_ar];
    }
}
