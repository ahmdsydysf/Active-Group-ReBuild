<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        if ($id === null) {
            return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('services', 'slug')->ignore($id)],
            'service_type_id' => 'required|integer|exists:service_types,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'body_ar' => 'nullable|string|max:200000',
            'body_en' => 'nullable|string|max:200000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'video_url' => 'nullable|string|max:500',
            'brochure_ar' => 'nullable|file|mimes:pdf|max:20480',
            'brochure_en' => 'nullable|file|mimes:pdf|max:20480',
            'published_at' => 'nullable|date',
            'remove_image' => 'nullable|boolean',
            'remove_brochure_ar' => 'nullable|boolean',
            'remove_brochure_en' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('services', 'slug')->ignore($id)],
            'service_type_id' => 'sometimes|required|integer|exists:service_types,id',
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'body_ar' => 'nullable|string|max:200000',
            'body_en' => 'nullable|string|max:200000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'video_url' => 'nullable|string|max:500',
            'brochure_ar' => 'nullable|file|mimes:pdf|max:20480',
            'brochure_en' => 'nullable|file|mimes:pdf|max:20480',
            'published_at' => 'nullable|date',
            'remove_image' => 'nullable|boolean',
            'remove_brochure_ar' => 'nullable|boolean',
            'remove_brochure_en' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
