<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Upload columns store a path relative to the public disk (or an absolute URL).
 */
trait HasFiles
{
    public function fileUrl(string $field): ?string
    {
        $path = $this->getAttribute($field);

        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function deleteFiles(): void
    {
        foreach ($this->fileFields ?? [] as $field) {
            $path = $this->getAttribute($field);
            if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
