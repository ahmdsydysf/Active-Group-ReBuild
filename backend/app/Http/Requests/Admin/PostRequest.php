<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($id)],
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'excerpt_ar' => 'nullable|string|max:5000',
            'excerpt_en' => 'nullable|string|max:5000',
            'body_ar' => 'nullable|string|max:200000',
            'body_en' => 'nullable|string|max:200000',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'published_at' => 'nullable|date',
            'remove_cover' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($id)],
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'excerpt_ar' => 'nullable|string|max:5000',
            'excerpt_en' => 'nullable|string|max:5000',
            'body_ar' => 'nullable|string|max:200000',
            'body_en' => 'nullable|string|max:200000',
            'cover' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'published_at' => 'nullable|date',
            'remove_cover' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
