<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\GalleryAlbum;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use Illuminate\Database\Eloquent\Model;

/** @extends ContentController<GalleryAlbum> */
#[AdminResource('gallery-albums', 'Admin · Gallery', 'GalleryAlbumAdmin')]
#[AdminRecord('gallery-albums', 'Admin · Gallery', 'GalleryAlbumAdmin')]
#[AdminReorder('gallery-albums', 'Admin · Gallery')]
#[AdminBulk('gallery-albums', 'Admin · Gallery')]
final class GalleryAlbumController extends ContentController
{
    protected string $model = GalleryAlbum::class;

    protected array $search = ['name_ar', 'name_en'];

    protected function rules(?Model $record): array
    {
        return [
            'name_ar' => [$record ? 'sometimes' : 'required', 'string', 'max:120'],
            'name_en' => 'nullable|string|max:120',
        ];
    }

    protected function present(Model $record): array
    {
        return [...parent::present($record), 'photos_count' => $record->photos()->count()];
    }
}
