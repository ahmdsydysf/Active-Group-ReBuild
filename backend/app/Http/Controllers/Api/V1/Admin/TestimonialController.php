<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Testimonial;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<Testimonial> */
#[AdminResource('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminRecord('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminReorder('testimonials', 'Admin · Testimonials')]
#[AdminBulk('testimonials', 'Admin · Testimonials')]
final class TestimonialController extends ContentController
{
    protected string $model = Testimonial::class;

    protected array $search = ['name_ar', 'name_en', 'quote_ar'];

    protected function rules(?Model $record): array
    {
        $required = $record ? 'sometimes' : 'required';

        return [
            'name_ar' => [$required, 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
            'role_ar' => 'nullable|string|max:160',
            'role_en' => 'nullable|string|max:160',
            'quote_ar' => [$required, 'string', 'max:600'],
            'quote_en' => 'nullable|string|max:600',
            'color' => ['nullable', Rule::in(Testimonial::COLORS)],
            'is_active' => 'nullable|boolean',
        ];
    }
}
