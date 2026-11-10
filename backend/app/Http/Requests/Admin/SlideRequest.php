<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlideRequest extends FormRequest
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
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:5000',
            'subtitle_en' => 'nullable|string|max:5000',
            'image' => 'required|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'link_url' => 'nullable|string|max:500',
            'remove_image' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'title_ar' => 'sometimes|required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:5000',
            'subtitle_en' => 'nullable|string|max:5000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'link_url' => 'nullable|string|max:500',
            'remove_image' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
