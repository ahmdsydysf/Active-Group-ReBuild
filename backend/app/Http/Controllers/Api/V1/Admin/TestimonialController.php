<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Testimonial;
use App\OpenApi\AdminCreate;
use App\OpenApi\AdminDelete;
use App\OpenApi\AdminList;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminUpdate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** @extends ContentController<Testimonial> */
#[AdminList('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminCreate('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminUpdate('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminDelete('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
#[AdminReorder('testimonials', 'Admin · Testimonials', 'TestimonialAdmin')]
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
