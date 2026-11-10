<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GalleryItemRequest extends FormRequest
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
            'gallery_category_id' => 'required|integer|exists:gallery_categories,id',
            'type' => 'required|in:image,video',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'video_url' => 'nullable|string|max:500',
            'caption_ar' => 'nullable|string|max:255',
            'caption_en' => 'nullable|string|max:255',
            'remove_media' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            ];
        }

        return [
            'gallery_category_id' => 'sometimes|required|integer|exists:gallery_categories,id',
            'type' => 'sometimes|required|in:image,video',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
            'video_url' => 'nullable|string|max:500',
            'caption_ar' => 'nullable|string|max:255',
            'caption_en' => 'nullable|string|max:255',
            'remove_media' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ];
    }
}
