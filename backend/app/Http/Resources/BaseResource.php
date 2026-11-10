<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Admin routes get every column (both languages + raw file paths);
 * public routes get a compact, localized shape.
 */
abstract class BaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (self::isAdmin($request)) {
            return $this->adminArray($request);
        }

        return $this->publicArray($request);
    }

    public static function isAdmin(Request $request): bool
    {
        return $request->is('api/v1/admin/*') || $request->is('api/v1/admin');
    }

    abstract protected function publicArray(Request $request): array;

    protected function adminRelations(Request $request): array
    {
        return [];
    }

    protected function adminArray(Request $request): array
    {
        $data = $this->resource->attributesToArray();

        foreach ($this->resource->fileFields ?? [] as $field) {
            $data[$field.'_url'] = $this->resource->fileUrl($field);
        }

        return array_merge($data, $this->adminRelations($request));
    }
}
