<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductTypeRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('product_types', 'slug')->ignore($id)],
            'product_category_id' => 'required|integer|exists:product_categories,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'remove_image' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('product_types', 'slug')->ignore($id)],
            'product_category_id' => 'sometimes|required|integer|exists:product_categories,id',
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'remove_image' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
