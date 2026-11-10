<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends FormRequest
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
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'website' => 'nullable|string|max:500',
            'facebook' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'remove_logo' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'website' => 'nullable|string|max:500',
            'facebook' => 'nullable|string|max:500',
            'twitter' => 'nullable|string|max:500',
            'instagram' => 'nullable|string|max:500',
            'remove_logo' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
