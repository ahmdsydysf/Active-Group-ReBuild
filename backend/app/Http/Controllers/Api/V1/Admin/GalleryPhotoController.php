<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\GalleryPhoto;
use App\OpenApi\AdminBulk;
use App\OpenApi\AdminRecord;
use App\OpenApi\AdminReorder;
use App\OpenApi\AdminResource;
use App\Support\ImageStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;
use RuntimeException;

/** @extends ContentController<GalleryPhoto> */
#[AdminResource('gallery-photos', 'Admin · Gallery', 'GalleryPhotoAdmin', ['album'])]
#[AdminRecord('gallery-photos', 'Admin · Gallery', 'GalleryPhotoAdmin')]
#[AdminReorder('gallery-photos', 'Admin · Gallery')]
#[AdminBulk('gallery-photos', 'Admin · Gallery')]
#[OA\Post(
    path: '/api/v1/admin/gallery-photos/upload',
    summary: 'Upload many photos at once into one album',
    security: [['sanctum' => []]],
    requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(mediaType: 'multipart/form-data', schema: new OA\Schema(properties: [
        new OA\Property(property: 'images[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary')),
        new OA\Property(property: 'gallery_album_id', type: 'integer'),
    ]))),
    tags: ['Admin · Gallery'],
    responses: [
        new OA\Response(response: 201, description: 'Uploaded', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/GalleryPhotoAdmin')),
        ])),
        new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
    ]
)]
final class GalleryPhotoController extends ContentController
{
    protected string $model = GalleryPhoto::class;

    protected array $search = ['caption_ar', 'caption_en'];

    protected array $requiredImages = ['image'];

    protected function rules(?Model $record): array
    {
        return [
            'gallery_album_id' => 'nullable|integer|exists:gallery_albums,id',
            'caption_ar' => 'nullable|string|max:255',
            'caption_en' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    protected function filter(Builder $query, Request $request): void
    {
        if ($album = $request->integer('album')) {
            $query->where('gallery_album_id', $album);
        }
    }

    /** Several images in one request; each becomes a photo at the end of the album. */
    public function upload(Request $request): JsonResponse
    {
        $data = $request->validate([
            'images' => 'required|array|min:1|max:20',
            'images.*' => ['required', 'file', ...explode('|', ImageStore::MIMES)],
            'gallery_album_id' => 'nullable|integer|exists:gallery_albums,id',
        ]);

        $order = (int) GalleryPhoto::query()->max('sort_order');
        $photos = DB::transaction(fn () => collect($data['images'])->map(function ($file, $i) use ($data, &$order) {
            try {
                $path = ImageStore::store($file, GalleryPhoto::UPLOAD_DIR);
            } catch (RuntimeException) {
                throw ValidationException::withMessages(["images.{$i}" => __('validation.image')]);
            }

            return GalleryPhoto::query()->create(['image' => $path, 'gallery_album_id' => $data['gallery_album_id'] ?? null, 'sort_order' => ++$order]);
        }));

        return response()->json(['data' => $photos->map(fn ($p) => $this->present($p))], 201);
    }
}
