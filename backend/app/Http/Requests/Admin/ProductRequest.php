<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($id)],
            'product_type_id' => 'required|integer|exists:product_types,id',
            'factory_id' => 'nullable|integer|exists:factories,id',
            'partner_id' => 'nullable|integer|exists:partners,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:200000',
            'description_en' => 'nullable|string|max:200000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'brochure_ar' => 'nullable|file|mimes:pdf|max:20480',
            'brochure_en' => 'nullable|file|mimes:pdf|max:20480',
            'is_featured' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
            'remove_brochure_ar' => 'nullable|boolean',
            'remove_brochure_en' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($id)],
            'product_type_id' => 'sometimes|required|integer|exists:product_types,id',
            'factory_id' => 'nullable|integer|exists:factories,id',
            'partner_id' => 'nullable|integer|exists:partners,id',
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:200000',
            'description_en' => 'nullable|string|max:200000',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'brochure_ar' => 'nullable|file|mimes:pdf|max:20480',
            'brochure_en' => 'nullable|file|mimes:pdf|max:20480',
            'is_featured' => 'nullable|boolean',
            'remove_image' => 'nullable|boolean',
            'remove_brochure_ar' => 'nullable|boolean',
            'remove_brochure_en' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
