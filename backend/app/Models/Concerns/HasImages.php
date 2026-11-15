<?php

namespace App\Models\Concerns;

use App\Support\ImageStore;

/**
 * Image columns hold a path on the public disk. Files are removed with the record.
 * Models list their image columns in `IMAGES` and their upload folder in `UPLOAD_DIR`.
 */
trait HasImages
{
    protected static function bootHasImages(): void
    {
        static::deleted(function (self $model): void {
            foreach (static::IMAGES as $field) {
                ImageStore::delete($model->getAttribute($field));
            }
        });
    }

    public function imageUrl(string $field = 'image', bool $small = false): ?string
    {
        return ImageStore::url($this->getAttribute($field), $small);
    }
}
