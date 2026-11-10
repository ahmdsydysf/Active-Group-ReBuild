<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TestimonialRequest extends FormRequest
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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'position_en' => 'nullable|string|max:255',
            'content_ar' => 'required|string|max:5000',
            'content_en' => 'nullable|string|max:5000',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'given_at' => 'nullable|date',
            'remove_photo' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'position_en' => 'nullable|string|max:255',
            'content_ar' => 'sometimes|required|string|max:5000',
            'content_en' => 'nullable|string|max:5000',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'given_at' => 'nullable|date',
            'remove_photo' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
